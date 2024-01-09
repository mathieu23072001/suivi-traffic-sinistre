// Créer la scène et la caméra
var scene = new THREE.Scene();
var camera = new THREE.PerspectiveCamera( 75, window.innerWidth / window.innerHeight, 0.1, 1000 );
camera.position.set( 0, 10, 20 );

// Créer le rendu
var renderer = new THREE.WebGLRenderer();
renderer.setSize( window.innerWidth, window.innerHeight );
document.getElementById('canvas-container').appendChild( renderer.domElement );

// Charger les données OSM
var loader = new THREE.OBJLoader();
loader.load(
    'https://www.mapzen.com/osm/buildings.obj',
    function ( object ) {
        scene.add( object );
    }
);

// Ajouter des lumières
var ambientLight = new THREE.AmbientLight( 0xffffff );
scene.add( ambientLight );

var directionalLight = new THREE.DirectionalLight( 0xffffff );
directionalLight.position.set( 0, 1, 1 );
scene.add( directionalLight );

// Animer la scène
function animate() {
    requestAnimationFrame( animate );
    renderer.render( scene, camera );
}
animate();
