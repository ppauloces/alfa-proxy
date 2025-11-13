import * as THREE from 'three'
import { gsap } from 'gsap'

export function initGlobe() {
  const canvas = document.getElementById('globeCanvas')
  if (!canvas) return

  // =========================
  // Cena + câmera + renderer
  // =========================
  const scene = new THREE.Scene()
  scene.background = null

  const camera = new THREE.PerspectiveCamera(
    45,
    canvas.clientWidth / canvas.clientHeight,
    0.1,
    1000
  )
  camera.position.z = 11

  const renderer = new THREE.WebGLRenderer({
    canvas,
    alpha: true,
    antialias: true
  })
  renderer.setPixelRatio(window.devicePixelRatio)
  renderer.setSize(canvas.clientWidth, canvas.clientHeight)

  // =========================
  // Iluminação tema proxy (azuis)
  // =========================
  const ambient = new THREE.AmbientLight(0x438ccb, 0.5)
  scene.add(ambient)

  const keyLight = new THREE.DirectionalLight(0x316fab, 1.2)
  keyLight.position.set(5, 3, 5)
  scene.add(keyLight)

  const rimLight = new THREE.PointLight(0x2a508a, 2, 50)
  rimLight.position.set(-5, 0, 5)
  scene.add(rimLight)

  const accentLight = new THREE.PointLight(0x306da8, 1.5, 40)
  accentLight.position.set(0, -5, -5)
  scene.add(accentLight)

  // =========================
  // GLOBO tema PROXY
  // =========================
  function createGlobe() {
    const group = new THREE.Group()

    // Esfera principal - cor azul gradient do site
    const geo = new THREE.SphereGeometry(3, 80, 80)
    const mat = new THREE.MeshPhongMaterial({
      color: 0x2055d5,
      emissive: 0x1e40af,
      emissiveIntensity: 0.3,
      shininess: 80,
      transparent: true,
      opacity: 0.85,
      wireframe: false
    })
    const globe = new THREE.Mesh(geo, mat)
    group.add(globe)

    // Wireframe overlay para dar efeito cyber/tech
    const wireGeo = new THREE.SphereGeometry(3.03, 40, 40)
    const wireMat = new THREE.MeshBasicMaterial({
      color: 0x60a5fa,
      wireframe: true,
      transparent: true,
      opacity: 0.25
    })
    const wireframe = new THREE.Mesh(wireGeo, wireMat)
    group.add(wireframe)

    // Linhas de latitude e longitude (grid)
    const gridMat = new THREE.LineBasicMaterial({
      color: 0xe8eef5,
      transparent: true,
      opacity: 0.3
    })

    // Criar linhas de latitude
    for (let lat = -80; lat <= 80; lat += 20) {
      const radius = 3.04 * Math.cos((lat * Math.PI) / 180)
      const y = 3.04 * Math.sin((lat * Math.PI) / 180)
      const curve = new THREE.EllipseCurve(0, 0, radius, radius, 0, 2 * Math.PI, false, 0)
      const points = curve.getPoints(50)
      const geometry = new THREE.BufferGeometry().setFromPoints(points)
      const line = new THREE.Line(geometry, gridMat)
      line.rotation.x = Math.PI / 2
      line.position.y = y
      group.add(line)
    }

    // Criar linhas de longitude
    for (let lon = 0; lon < 180; lon += 20) {
      const curve = new THREE.EllipseCurve(0, 0, 3.04, 3.04, 0, 2 * Math.PI, false, 0)
      const points = curve.getPoints(50)
      const geometry = new THREE.BufferGeometry().setFromPoints(points)
      const line = new THREE.Line(geometry, gridMat)
      line.rotation.y = (lon * Math.PI) / 180
      group.add(line)
    }

    // Atmosfera / glow externo
    const haloGeo = new THREE.SphereGeometry(3.5, 64, 64)
    const haloMat = new THREE.ShaderMaterial({
      uniforms: {
        c: { value: 0.5 },
        p: { value: 4.0 }
      },
      vertexShader: `
        varying vec3 vNormal;
        void main() {
          vNormal = normalize(normalMatrix * normal);
          gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
        }
      `,
      fragmentShader: `
        uniform float c;
        uniform float p;
        varying vec3 vNormal;
        void main() {
          float intensity = pow(c - dot(vNormal, vec3(0.0, 0.0, 1.0)), p);
          gl_FragColor = vec4(0.19, 0.42, 0.84, 1.0) * intensity;
        }
      `,
      side: THREE.BackSide,
      blending: THREE.AdditiveBlending,
      transparent: true
    })
    const halo = new THREE.Mesh(haloGeo, haloMat)
    group.add(halo)

    return { group, globe }
  }

  // =========================
  // SERVIDORES PROXY (nodes)
  // =========================
  function createNodes() {
    const group = new THREE.Group()

    // Mais servidores para dar ideia de rede global
    const positions = [
      { lat: 40.7128, lon: -74.0060, name: 'NY' },      // Nova York
      { lat: 51.5074, lon: -0.1278, name: 'LON' },      // Londres
      { lat: 35.6762, lon: 139.6503, name: 'TYO' },     // Tóquio
      { lat: -23.5505, lon: -46.6333, name: 'SP' },     // São Paulo
      { lat: 1.3521, lon: 103.8198, name: 'SG' },       // Singapura
      { lat: 52.5200, lon: 13.4050, name: 'BER' },      // Berlim
      { lat: -33.8688, lon: 151.2093, name: 'SYD' },    // Sydney
      { lat: 37.7749, lon: -122.4194, name: 'SF' },     // San Francisco
      { lat: 55.7558, lon: 37.6173, name: 'MOS' },      // Moscow
      { lat: 19.4326, lon: -99.1332, name: 'MEX' }      // Mexico City
    ]

    function latLonToVector3(lat, lon, radius) {
      const phi = (90 - lat) * (Math.PI / 180)
      const theta = (lon + 180) * (Math.PI / 180)
      return new THREE.Vector3(
        -(radius * Math.sin(phi) * Math.cos(theta)),
        radius * Math.cos(phi),
        radius * Math.sin(phi) * Math.sin(theta)
      )
    }

    positions.forEach((pos, index) => {
      const basePos = latLonToVector3(pos.lat, pos.lon, 3.08)

      // Servidor (node maior)
      const nodeGeo = new THREE.SphereGeometry(0.09, 16, 16)
      const nodeMat = new THREE.MeshBasicMaterial({
        color: 0xe8eef5,
        transparent: true,
        opacity: 1
      })
      const node = new THREE.Mesh(nodeGeo, nodeMat)
      node.position.copy(basePos)
      group.add(node)

      // Pulso ao redor (indicador de atividade)
      const pulseGeo = new THREE.SphereGeometry(0.15, 16, 16)
      const pulseMat = new THREE.MeshBasicMaterial({
        color: 0x60a5fa,
        transparent: true,
        opacity: 0.5
      })
      const pulse = new THREE.Mesh(pulseGeo, pulseMat)
      pulse.position.copy(basePos)
      group.add(pulse)

      // Animação de breathing + scale pulse
      const delay = index * 0.25
      gsap.to(nodeMat, {
        opacity: 0.4,
        duration: 1.8,
        repeat: -1,
        yoyo: true,
        ease: 'sine.inOut',
        delay
      })
      gsap.to(pulse.scale, {
        x: 1.5,
        y: 1.5,
        z: 1.5,
        duration: 1.8,
        repeat: -1,
        yoyo: true,
        ease: 'sine.inOut',
        delay
      })
      gsap.to(pulseMat, {
        opacity: 0.1,
        duration: 1.8,
        repeat: -1,
        yoyo: true,
        ease: 'sine.inOut',
        delay
      })
    })

    return group
  }

  // =========================
  // CONEXÕES entre servidores (linhas de dados)
  // =========================
  function createConnections() {
    const group = new THREE.Group()

    const connections = [
      [0, 1], [1, 5], [2, 4], [3, 0],
      [4, 6], [5, 7], [7, 2], [6, 3],
      [8, 1], [9, 0], [8, 5], [9, 3]
    ]

    const positions = [
      { lat: 40.7128, lon: -74.0060 },
      { lat: 51.5074, lon: -0.1278 },
      { lat: 35.6762, lon: 139.6503 },
      { lat: -23.5505, lon: -46.6333 },
      { lat: 1.3521, lon: 103.8198 },
      { lat: 52.5200, lon: 13.4050 },
      { lat: -33.8688, lon: 151.2093 },
      { lat: 37.7749, lon: -122.4194 },
      { lat: 55.7558, lon: 37.6173 },
      { lat: 19.4326, lon: -99.1332 }
    ]

    function latLonToVector3(lat, lon, radius) {
      const phi = (90 - lat) * (Math.PI / 180)
      const theta = (lon + 180) * (Math.PI / 180)
      return new THREE.Vector3(
        -(radius * Math.sin(phi) * Math.cos(theta)),
        radius * Math.cos(phi),
        radius * Math.sin(phi) * Math.sin(theta)
      )
    }

    connections.forEach((conn, idx) => {
      const start = latLonToVector3(positions[conn[0]].lat, positions[conn[0]].lon, 3.08)
      const end = latLonToVector3(positions[conn[1]].lat, positions[conn[1]].lon, 3.08)

      // Ponto médio elevado para curva
      const mid = new THREE.Vector3()
        .addVectors(start, end)
        .multiplyScalar(0.5)
        .normalize()
        .multiplyScalar(4.0)

      const curve = new THREE.QuadraticBezierCurve3(start, mid, end)
      const points = curve.getPoints(50)
      const lineGeometry = new THREE.BufferGeometry().setFromPoints(points)

      const lineMaterial = new THREE.LineBasicMaterial({
        color: 0x60a5fa,
        transparent: true,
        opacity: 0.4
      })

      const line = new THREE.Line(lineGeometry, lineMaterial)
      group.add(line)

      // Partícula de dados viajando
      const particleGeo = new THREE.SphereGeometry(0.04, 8, 8)
      const particleMat = new THREE.MeshBasicMaterial({
        color: 0xe8eef5,
        transparent: true,
        opacity: 1
      })
      const particle = new THREE.Mesh(particleGeo, particleMat)
      group.add(particle)

      // Trail da partícula
      const trailGeo = new THREE.SphereGeometry(0.06, 8, 8)
      const trailMat = new THREE.MeshBasicMaterial({
        color: 0x2055d5,
        transparent: true,
        opacity: 0.3
      })
      const trail = new THREE.Mesh(trailGeo, trailMat)
      group.add(trail)

      // Animar partícula
      const duration = 2.5 + idx * 0.3
      gsap.to({ t: 0 }, {
        t: 1,
        duration,
        repeat: -1,
        ease: 'none',
        delay: idx * 0.3,
        onUpdate: function() {
          const t = this.targets()[0].t
          const point = curve.getPoint(t)
          particle.position.copy(point)

          const trailT = Math.max(0, t - 0.05)
          const trailPoint = curve.getPoint(trailT)
          trail.position.copy(trailPoint)
        }
      })

      // Pulse da partícula
      gsap.to(particleMat, {
        opacity: 0.5,
        duration: 0.8,
        repeat: -1,
        yoyo: true,
        ease: 'sine.inOut'
      })
    })

    return group
  }

  // =========================
  // Montagem da cena
  // =========================
  const { group: globeGroup } = createGlobe()
  const nodesGroup = createNodes()
  const connectionsGroup = createConnections()

  scene.add(globeGroup)
  scene.add(connectionsGroup)
  scene.add(nodesGroup)

  // =========================
  // Parallax do mouse
  // =========================
  let mouseX = 0, mouseY = 0
  let targetX = 0, targetY = 0

  document.addEventListener('mousemove', (e) => {
    mouseX = (e.clientX / window.innerWidth) * 2 - 1
    mouseY = -(e.clientY / window.innerHeight) * 2 + 1
  })

  // =========================
  // Loop de animação
  // =========================
  function animate() {
    requestAnimationFrame(animate)

    // Rotação lenta
    globeGroup.rotation.y += 0.002
    nodesGroup.rotation.y += 0.002
    connectionsGroup.rotation.y += 0.002

    // Parallax suave
    targetX = mouseX * 0.3
    targetY = mouseY * 0.3

    camera.position.x += (targetX - camera.position.x) * 0.05
    camera.position.y += (targetY - camera.position.y) * 0.05
    camera.lookAt(0, 0, 0)

    renderer.render(scene, camera)
  }

  animate()

  // =========================
  // Resize
  // =========================
  window.addEventListener('resize', () => {
    const w = canvas.clientWidth
    const h = canvas.clientHeight
    camera.aspect = w / h
    camera.updateProjectionMatrix()
    renderer.setSize(w, h)
  })

  // =========================
  // GSAP – entrada suave
  // =========================
  gsap.from(canvas, {
    opacity: 0,
    scale: 0.85,
    duration: 1.4,
    ease: 'power3.out'
  })
}
