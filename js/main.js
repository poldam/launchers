
$(document).ready(function() {
    $('#template').on('change', function() {
        var templateId = $(this).val();
        if (templateId) {
            // Make AJAX call to fetch template data
            $.ajax({
                url: './scripts/get_templates.php', // PHP script to fetch the template data
                type: 'POST',
                data: { templateId: templateId },
                success: function(response) {
                    // Parse the JSON response
                    var template = JSON.parse(response);
                    // console.log(template)
                    $('#name').val(template.name);
                    $('#launcherdescription').text(template.description);
                }
            });
        } else {
            // Clear the form fields if no template is selected
            $('#launcherForm')[0].reset();
        }
    });
});

// custom launcher icon
var launcherIcon = L.icon({
    iconUrl: './images/launcher-icon.png', 
    iconSize: [38, 38], 
    iconAnchor: [19, 38], 
    popupAnchor: [0, -40] 
});

// custom airdefense icon
var defenceIcon = L.icon({
    iconUrl: './images/airdefense-icon.png', 
    iconSize: [38, 38], 
    iconAnchor: [19, 38], 
    popupAnchor: [0, -40] 
});

var map = L.map('map', {zoomControl: false}).setView([37.9838, 23.7275], 4); // Εστίαση στην Αθήνα

var osm = L.tileLayer ('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: ''
}).addTo (map);

// Streets
googleStreets = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',{
    maxZoom: 20,
    subdomains:['mt0','mt1','mt2','mt3']
});

// Hybrid:
googleHybrid = L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}',{
    maxZoom: 20,
    subdomains:['mt0','mt1','mt2','mt3']
});

// Satellite:
googleSat = L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',{
    maxZoom: 20,
    subdomains:['mt0','mt1','mt2','mt3']
});

// Terrain
googleTerrain = L.tileLayer('http://{s}.google.com/vt/lyrs=p&x={x}&y={y}&z={z}',{
    maxZoom: 20,
    subdomains:['mt0','mt1','mt2','mt3']
});

var cartoDBPositron = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    attribution: '',
    subdomains: 'abcd',
    maxZoom: 19
});

var cartoDBDarkMatter = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
    attribution: '',
    subdomains: 'abcd',
    maxZoom: 19
});

var powerGridLayer = L.tileLayer('https://tiles.openinframap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: ''
})

var flosmPowerGridLayer = L.tileLayer('https://{s}.flosm.de/power/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: ''
})

var osmPowerLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: ''
})

var baseMaps = {
    "OSM": osm,
    "Streets": googleStreets,
    "Satelite": googleSat,
    "Hybrid": googleHybrid,
    "Terain": googleTerrain,
    "Bright": cartoDBPositron,
    "Dark Matter": cartoDBDarkMatter
};

L.control.layers(baseMaps, null, { position: 'topright', collapsed: false }).addTo(map);

//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
//// NUCLEAR SITES
const nuclearFactoriesLayer = L.layerGroup();
const nuclearIcon = L.divIcon({
    html: '<i class="nuclear-icon">&#9762;</i>',
    className: 'nuclear-icon',
    iconSize: [45, 45], // Size of the icon
    iconAnchor: [12, 12] // Anchor point of the icon
});
// Load the JSON data
// https://github.com/cristianst85/GeoNuclearData/blob/master/data/json/raw/4-nuclear_power_plants.json
fetch('./assets/nuclear.json')
    .then(response => response.json())
    .then(data => {
        data.forEach(plant => {
            const { Name, Latitude, Longitude, ReactorModel, OperationalFrom, OperationalTo, Capacity } = plant;
            
            if((OperationalTo === null || OperationalTo === "") && Latitude && Longitude) {
                const marker = L.marker([Latitude, Longitude],{ icon: nuclearIcon });

                marker.bindPopup(`
                    <b>${Name}</b><br>
                    Reactor Model: ${ReactorModel || "N/A"}<br>
                    Operational From: ${OperationalFrom || "N/A"}<br>
                    Capacity: ${Capacity || "N/A"} MW
                `);

                nuclearFactoriesLayer.addLayer(marker);
            }
        });
    })
    .catch(error => console.error('Error loading nuclear JSON data:', error));

//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
// Define the war areas array
var ukraineWar = L.layerGroup() //.addTo(map);

const warareas = [
    // regions
    ['donetsk', '#555555'],
    ['lugansk', '#555555'],
    ['zaporizhia', '#555555'],
    ['cherson', '#555555'],
    ['krim', '#555555'],
    ['transnistria', '#555555']
];

// Loop through each area and fetch its GeoJSON file
warareas.forEach(area => {
    const city = area[0];
    const cityColor = area[1];

    // Fetch GeoJSON data for the city
    fetch(`./assets/warcities/${city}.geo.json`)
        .then(response => response.json())
        .then(json => {
            // Create a Leaflet GeoJSON layer
            const region = L.geoJson(json, {
                style: {
                    fillColor: cityColor,
                    fillOpacity: 0.1,
                    weight: 1,
                    color: '#000' // Optional: Outline color
                }
            });
            // Add the layer to the map
            region.addTo(ukraineWar);
        })
        .catch(error => console.error(`Error fetching GeoJSON for ${city}:`, error));
});

////////// BRICS //////////
var brics = L.layerGroup() //.addTo(map); // Initialize the layer group and add it to the map
var g7 = L.layerGroup()
var g20 = L.layerGroup()
var nato = L.layerGroup()
var shangai = L.layerGroup()
var ww2axis = L.layerGroup()
var ww2allies = L.layerGroup()
var fiveeyes = L.layerGroup()
var eu = L.layerGroup()
var eok = L.layerGroup()
var myjson = null;

// Fetch the GeoJSON data for countries
fetch("./assets/countries.geo.json")
    .then(response => response.json())
    .then(json => {
        myjson = json; // Store the GeoJSON data

        // Function to add countries to the layer group
        function addCountriesToLayerGroup(countries, fillcolor, actualLayer) {
            countries.forEach(countryName => {
                json.features.forEach(feature => {
                    if (feature.properties.name === countryName) {
                        // Create a GeoJSON layer for the matching country
                        const country = L.geoJson(feature, {
                            style: {
                                fillColor: fillcolor,
                                fillOpacity: 0.2, // Adjust transparency as needed
                                weight: 1,
                                color: '#000' // Optional: Outline color
                            }
                        });

                        // Add the country layer to the layer group
                        actualLayer.addLayer(country);
                    }
                });
            });
        }

        // Add BRICS countries
        addCountriesToLayerGroup(
            ['China', 'Russia', 'Brazil', 'South Africa', 'India'],
            '#008f00', brics
        );

        // Add BRICS+ countries
        addCountriesToLayerGroup(
            ['Iran', 'Ethiopia', 'Saudi Arabia', 'United Arab Emirates', 'Egypt'],
            '#adff2f', brics
        );

        // Add G7 countries
        addCountriesToLayerGroup(
            ['United States of America', 'Canada', 'Germany', 'Japan', 'United Kingdom', 'Italy', 'France'],
            '#ff0000', g7
        );

        // UkraineWar
        addCountriesToLayerGroup(
            ['Russia'],
            '#0000FF', ukraineWar
        );

        addCountriesToLayerGroup(
            ['Ukraine'],
            '#FFFF00', ukraineWar
        );

        addCountriesToLayerGroup(
            ['Finland','Georgia','Moldova','Estonia','Latvia','Lithuania'],
            '#555555', ukraineWar
        );

        addCountriesToLayerGroup(
            ['Belarus'],
            '#87CEEB', ukraineWar
        );

        addCountriesToLayerGroup(
            ['Australia','Canada','Saudi Arabia','United States of America','India','Russia','South Africa','Turkey','Argentina','Brazil','Mexico','France','Germany','Italy','United Kingdom','China','Indonesia','Japan','South Korea'],
            '#FFD700', g20
        );

        const natoCountries = [
            "Albania",
            "Belgium",
            "Bulgaria",
            "Canada",
            "Croatia",
            "Czech Republic",
            "Denmark",
            "Estonia",
            "Finland",
            "France",
            "Germany",
            "Greece",
            "Hungary",
            "Iceland",
            "Italy",
            "Latvia",
            "Lithuania",
            "Luxembourg",
            "Montenegro",
            "Netherlands",
            "Macedonia",
            "Norway",
            "Poland",
            "Portugal",
            "Romania",
            "Slovakia",
            "Slovenia",
            "Spain",
            "Sweden",
            "Turkey",
            "United Kingdom",
            "United States of America"
        ];

        addCountriesToLayerGroup(
            natoCountries,
            '#004990', nato
        );

        const scoMemberStates = [
            "Belarus",
            "China",
            "India",
            "Iran",
            "Kazakhstan",
            "Kyrgyzstan",
            "Pakistan",
            "Russia",
            "Tajikistan",
            "Uzbekistan"
        ];

        addCountriesToLayerGroup(
            scoMemberStates,
            '#C1E4D0', shangai
        );

        const axisPowers = [
            "Germany",
            "Italy",
            "Japan",
            "Hungary",
            "Romania",
            "Bulgaria",
            "Slovakia",
            "Croatia",
            "Finland"
        ];
          
        addCountriesToLayerGroup(
            axisPowers,
            '#ff0000', ww2axis
        );

        const fiveEyesCountries = [
            "Australia",
            "Canada",
            "New Zealand",
            "United Kingdom",
            "United States of America"
        ];

        addCountriesToLayerGroup(
            fiveEyesCountries,
            '#0033A0', fiveeyes
        );
        
        const alliedPowers = [
            "United States of America",
            "Armenia",
            "Azerbaijan",
            "Belarus",
            "Estonia",
            "Georgia",
            "Kazakhstan",
            "Kyrgyzstan",
            "Latvia",
            "Lithuania",
            "Moldova",
            "Russia",
            "Tajikistan",
            "Turkmenistan",
            "Ukraine",
            "Uzbekistan",
            "United Kingdom",
            "China",
            "France",
            "Poland",
            "Canada",
            "Australia",
            "New Zealand",
            "India",
            "South Africa",
            "Belgium",
            "Norway",
            "Netherlands",
            "Greece",
            "Czech Republic",
            "Bosnia and Herzegovina",
            "Croatia",
            "North Macedonia",
            "Montenegro",
            "Serbia",
            "Slovenia",
            "Kosovo",
            "Brazil"
        ];

        addCountriesToLayerGroup(
            alliedPowers,
            '#012169', ww2allies
        );

        const euCountries = [
            "Austria",
            "Belgium",
            "Bulgaria",
            "Croatia",
            "Cyprus",
            "Czech Republic",
            "Denmark",
            "Estonia",
            "Finland",
            "France",
            "Germany",
            "Greece",
            "Hungary",
            "Ireland",
            "Italy",
            "Latvia",
            "Lithuania",
            "Luxembourg",
            "Malta",
            "Netherlands",
            "Poland",
            "Portugal",
            "Romania",
            "Slovakia",
            "Slovenia",
            "Spain",
            "Sweden"
        ];

        addCountriesToLayerGroup(
            euCountries,
            '#0034A0', eu
        );

        const eurozoneCountries = [
            "Austria",
            "Belgium",
            "Croatia",
            "Cyprus",
            "Estonia",
            "Finland",
            "France",
            "Germany",
            "Greece",
            "Ireland",
            "Italy",
            "Latvia",
            "Lithuania",
            "Luxembourg",
            "Malta",
            "Netherlands",
            "Portugal",
            "Slovakia",
            "Slovenia",
            "Spain"
        ];

        addCountriesToLayerGroup(
            eurozoneCountries,
            '#0047A0', eok
        );
          
    })
    .catch(error => console.error("Error fetching or processing GeoJSON data:", error));

//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////

var selectedLauncherId = null;
var currentMarker = null;
var rangeCircle = null;

var userMarker = null; // Store the user location marker
var userCircle = null; // Store the user location circle

// Load all stored launchers
function loadLaunchers() {
    $.ajax({
        url: './scripts/get_launchers.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            data.forEach(function(launcher) {
                addLauncherToMap(launcher);
            });
        }
    });
}

function loadAirDefenses() {
    $.ajax({
        url: './scripts/get_airdefenses.php', // Fetch air defenses from the server
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            data.forEach(function(airDefense) {
                addAirDefenseToMap(airDefense); // Add each air defense to the map
            });
        },
        error: function(xhr, status, error) {
            console.error("Error fetching air defenses:", error);
        }
    });
}

///////////////////////////////////////////
// FIND MY LOCATION ///////////////////////
// Add custom button for finding location
L.Control.LocateControl = L.Control.extend({
    onAdd: function() {
        var div = L.DomUtil.create('div', 'leaflet-control-locate');
        div.innerHTML = 'Find My Location';
        div.onclick = function(e) {
            L.DomEvent.stopPropagation(e);
            findMyLocation();
        };
        return div;
    }
});

// Add button to the map
L.control.locateControl = function(opts) {
    return new L.Control.LocateControl(opts);
}

L.control.locateControl({ position: 'bottomleft' }).addTo(map);

// Find and locate the user's position
function findMyLocation() {
    map.locate({setView: true, maxZoom: 16});

    map.on('locationfound', function(e) {
        // If marker or circle already exists, update their position
        if (userMarker) {
            userMarker.setLatLng(e.latlng).openPopup();
        } else {
            userMarker = L.marker(e.latlng).addTo(map)
                .bindPopup('You are here').openPopup();
        }

        // Update or create the accuracy circle
        if (userCircle) {
            userCircle.setLatLng(e.latlng).setRadius(e.accuracy);
        } else {
            userCircle = L.circle(e.latlng, {radius: e.accuracy}).addTo(map);
        }
    });

    map.on('locationerror', function(e) {
        alert("Location access denied or not available.");
    });
}

//////////////////////////////////////////////////
//////////////////////////////////////////////////

// Create separate layers for launchers, range circles, and blasts
var launcherLayer = L.layerGroup().addTo(map);
var blastLayer = L.layerGroup().addTo(map);
// Initialize the air defense layer
var airDefenseLayer = L.layerGroup().addTo(map); // Add the air defense layer to the map


// Array to store launcher data
var launchers = [];
var airDefenses = [];
var detectedAirDefenses = [];

var defenseStatistics = [];

function getAggressiveColor() {
    // Generate random aggressive colors with more emphasis on red and dark tones
    let r = Math.floor(Math.random() * 156 + 100); // Red channel - higher intensity
    let g = Math.floor(Math.random() * 100); // Green channel - low to medium intensity
    let b = Math.floor(Math.random() * 100); // Blue channel - low to medium intensity

    // Convert to hex and return the color
    return '#' + r.toString(16).padStart(2, '0') + g.toString(16).padStart(2, '0') + b.toString(16).padStart(2, '0');
}

// Add launcher to map with a range circle and draggable marker
function addLauncherToMap(launcher) {
    var randomColor = getAggressiveColor();

    var marker = L.marker([launcher.lat, launcher.lng], { draggable: true, icon: launcherIcon }).addTo(launcherLayer); // Add to launcher layer
    var circle = L.circle([launcher.lat, launcher.lng], {
        radius: launcher.range,
        color: randomColor,  // Use random color for the range circle
        fillOpacity: 0.3
    }).addTo(launcherLayer); // Add to launcher layer

    // Store launcher and circle data in the array
    launchers.push({
        id: launcher.id,
        templateID: launcher.templateID,
        name: launcher.name,
        model: launcher.model,
        rocketName: launcher.rocket_name,
        lat: launcher.lat,
        lng: launcher.lng,
        range: launcher.range,
        blastRadius: launcher.blast_radius,
        speed: launcher.speed,
        color: randomColor,
        marker: marker,
        circle: circle
    });

    // Ensure launcher.range and blast_radius are treated as numbers
    var rangeMeters = parseFloat(launcher.range);
    var rangeKilometers = (rangeMeters / 1000).toFixed(2);
    var blastRadiusMeters = parseFloat(launcher.blast_radius);

    // Display info on hover (tooltip with name, model, max range, and blast radius)
    marker.bindTooltip(`
        <b>${launcher.name}</b><br>
        Model: ${launcher.model}<br>
        Rocket: ${launcher.rocket_name}<br>
        Speed: ${launcher.speed} m/s  (${(launcher.speed / 343).toFixed(2)} Mach)<br>
        Max Range: ${rangeKilometers} km<br>
        Blast Radius: ${blastRadiusMeters.toFixed(2)} m
    `, {
        permanent: false,
        direction: 'top',
        offset: [0, -20]
    });

    // Left-click to edit launcher (open modal)
    marker.on('click', function () {
        fillLauncherForm(launcher); // Fill form with data for editing
        $('#launcherModal').modal('show'); // Show modal for editing
    });

    // Update launcher position after drag
    marker.on('dragend', function (event) {
        var newPos = event.target.getLatLng();
        launcher.lat = newPos.lat; // Update launcher latitude
        launcher.lng = newPos.lng; // Update launcher longitude
        circle.setLatLng(newPos); // Update circle position on the map

        // Update launcher data in the launchers array
        var launcherIndex = launchers.findIndex(l => l.id === launcher.id);
        if (launcherIndex > -1) {
            launchers[launcherIndex].lat = newPos.lat;
            launchers[launcherIndex].lng = newPos.lng;
        }

        updateLauncherPosition(launcher.id, newPos.lat, newPos.lng);
    });

    // Right-click to delete launcher
    marker.on('contextmenu', function () {
        selectedLauncherId = launcher.id;
        $('#deleteModal').modal('show');
    });
}

function addAirDefenseToMap(airDefense) {
    var detectionCircleColor = '#3498db';  // Blue for detection range
    var interceptionCircleColor = 'green';  // Red for interception range (dashed)

    // Create the air defense marker
    var marker = L.marker([airDefense.lat, airDefense.lng], { draggable: true, icon: defenceIcon }).addTo(airDefenseLayer);

    // Detection range circle
    var detectionCircle = L.circle([airDefense.lat, airDefense.lng], {
        radius: airDefense.detection_range,
        color: detectionCircleColor,
        fillOpacity: 0.1,
        dashArray: '5, 10' // Dashed line for interception range
    }).addTo(airDefenseLayer);

    // Interception range circle (dashed border)
    // console.log(airDefense)
    var interceptionCircle = L.circle([airDefense.lat, airDefense.lng], {
        radius: airDefense.interception_range,
        color: interceptionCircleColor,
        fillOpacity: 0.1
    }).addTo(airDefenseLayer);

    // Store air defense data in an array
    airDefenses.push({
        id: airDefense.id,
        name: airDefense.name,
        lat: airDefense.lat,
        lng: airDefense.lng,
        detectionRange: airDefense.detection_range,
        interceptionRange: airDefense.interception_range,
        interceptionSpeed: airDefense.interception_speed,
        description: airDefense.description,
        total: airDefense.total,
        success: airDefense.success,
        failure: airDefense.failure,
        accuracy: airDefense.accuracy,
        maxSimultaneousTargets: airDefense.max_simultaneous_targets,
        reloadTime: airDefense.reload_time,
        numRockets: airDefense.num_rockets,
        marker: marker,
        detectionCircle: detectionCircle,
        interceptionCircle: interceptionCircle,
        reactionTime: airDefense.reaction_time,
        isHypersonicCapable: airDefense.isHypersonicCapable
    });

    var rating = ' - ';
    // console.log(airDefense);
    if(parseFloat(airDefense.total) > 0) {
        rating = `${(parseFloat(airDefense.success)*100/parseFloat(airDefense.total)).toFixed(2)}% - T: ${airDefense.total} S: ${airDefense.success} F: ${airDefense.failure}`;
    }

    // Display info on hover
    marker.bindTooltip(`
        <b>${airDefense.name}</b><br>
        Model: ${airDefense.model}<br>
        Detection Range: ${(airDefense.detection_range / 1000).toFixed(2)} km<br>
        Interception Range: ${(airDefense.interception_range / 1000).toFixed(2)} km<br>
        Number of Rockets: ${airDefense.num_rockets}/launcher <br>
        Reload time: ${airDefense.reload_time} mins<br>
        Simultanious targets: ${airDefense.max_simultaneous_targets}<br>
        Interception speed: ${airDefense.interception_speed} m/s (${(airDefense.interception_speed / 343).toFixed(2)} Mach)<br>
        Reaction time: ${airDefense.reaction_time} s<br>
        Accuracy: ${parseFloat(airDefense.accuracy)*100}%<br>
        Success Rate: ${rating}
    `, {
        permanent: false,
        direction: 'top',
        offset: [0, -20]
    });

    // Handle air defense editing on left-click
    marker.on('click', function () {
        fillAirDefenseForm(airDefense); // Fill the form with the air defense data
        $('#airDefenseModal').modal('show'); // Show the modal to edit
    });

    // Update air defense position on drag end
    marker.on('dragend', function (event) {
        var newPos = event.target.getLatLng();
        airDefense.lat = newPos.lat;
        airDefense.lng = newPos.lng;

        // Update the detection and interception circle positions
        detectionCircle.setLatLng(newPos);
        interceptionCircle.setLatLng(newPos);

        // Update airDefense data in the airDefenses array
        var airDefenseIndex = airDefenses.findIndex(l => l.id === airDefense.id);
        if (airDefenseIndex > -1) {
            airDefenses[airDefenseIndex].lat = newPos.lat;
            airDefenses[airDefenseIndex].lng = newPos.lng;
        }

        updateAirDefensePosition(airDefense.id, newPos.lat, newPos.lng);
    });

    // Handle air defense deletion on right-click
    marker.on('contextmenu', function () {
        selectedAirDefenseId = airDefense.id;
        $('#airdeleteModal').modal('show');  // Show delete confirmation modal
    });
}

// Simulate interception success based on multiple realistic factors
function determineInterceptionSuccess(airDefense, timeToImpact, interceptionTime, distanceToTarget, closestLauncher) {
    var randomFactor = Math.random();
    
    // Factor 1: Speed of the incoming missile vs air defense interception speed
    const HYPERSONIC = 1715, SUPERSONIC = 412; // Launcher speeds in m/s
    
    var missileSpeedRatio = closestLauncher.speed / airDefense.interceptionSpeed; // Ratio of missile speed to interception speed
    var speedPenalty = 0.2 * (missileSpeedRatio - 1); // Gradual penalty for faster missiles
    var speedFactor;
    
    if (closestLauncher.speed >= HYPERSONIC) {
        speedFactor = airDefense.isHypersonicCapable ? 1 - speedPenalty : 0.1; // Hypersonic missiles (e.g., Kinzhal) are nearly impossible to intercept unless countered by hypersonic systems
    } else if (closestLauncher.speed >= SUPERSONIC) {
        speedFactor = 1 - speedPenalty; 
    } else {
        speedFactor = 1.0; // No penalty for slower missiles
    }
     
    // Factor 2: Early detection bonus - higher success if detected early
    var earlyDetectionBonus = (distanceToTarget < airDefense.interceptionRange * 0.7) ? 1.1 : 1.0; // 10% bonus for early detection

    // Factor 3: Multiple targets - degrade effectiveness if multiple missiles are incoming
    var targetOverloadFactor = (airDefense.numTrackedTargets > airDefense.maxSimultaneousTargets) ? 0.8 : 1.0; // Decrease if overloaded

    // Factor 4: Environmental randomness - simulating interference or weather
    var environmentFactor = 1 - (Math.random() * 0.05); // Small random degradation (e.g., jamming, fog)

    // Factor 5: Hypersonic countermeasures
    var hypersonicBonus = closestLauncher.speed > 5000 && airDefense.isHypersonicCapable ? 1.2 : 1.0; // Boost if air defense can handle hypersonics

    // Final accuracy is a combination of the system's base accuracy and modifiers
    var modifiedAccuracy = airDefense.accuracy 
        * speedFactor 
        * earlyDetectionBonus 
        * targetOverloadFactor 
        * environmentFactor 
        * hypersonicBonus;
    
    // Determine interception success
    return randomFactor <= modifiedAccuracy && interceptionTime <= timeToImpact;
}
//////////////////////////////////////////////////////
// BLASTS AND INTERCEPTIONS ////////////////////////
//////////////////////////////////////////////////////

// Speed Classification	Mach Number
// 1 Mach = 340.29 m/s
// Subsonic	Mach < 1.0 
// Transonic	Mach = 1.0
// Supersonic	Mach > 1.0
// Hypersonic	Mach > 5.0

// Right-click to set target and find the closest launcher in range
map.on('contextmenu', function (e) {
    var targetLat = e.latlng.lat;
    var targetLng = e.latlng.lng;
    var closestLauncher = null;
    var closestDistance = Infinity;
    
    // Find the closest launcher that is in range
    launchers.forEach(function (launcher) {
        var distance = map.distance([launcher.lat, launcher.lng], [targetLat, targetLng]);

        // Check if this launcher is closer and within range
        if (distance <= launcher.range && distance < closestDistance) {
            closestLauncher = launcher;
            closestDistance = distance;
        }
    });

    // If no launcher is in range, show a message
    if (!closestLauncher) {
        alert('This point is not reachable by any launcher.');
        return;
    }

    // Create the blast
    var blastColor = closestLauncher.color;
    var blastCircle = L.circle([targetLat, targetLng], {
        radius: closestLauncher.blastRadius,
        color: blastColor,
        fillColor: 'orange',
        fillOpacity: 1,
        weight: 6
    });

    // Store potential interceptors
    var interceptors = [];

    // Check which air defenses can intercept the blast (based on interception range)
    airDefenses.forEach(function (airDefense) {
        var distanceToTarget = map.distance([airDefense.lat, airDefense.lng], [targetLat, targetLng]);

        // Log all air defenses that detect the incoming missile (based on detection range)
        if (distanceToTarget <= airDefense.detectionRange) {
            console.log(`${airDefense.name} detected the incoming missile!`);

            // TODO: Store detected air defenses if needed for further analysis
            detectedAirDefenses.push(airDefense);
        }

        if (distanceToTarget <= airDefense.interceptionRange) {
            // Calculate interception time and store in interceptors array
            var interceptionTime = calculateInterceptionTime(airDefense, targetLat, targetLng);
            interceptors.push({ airDefense: airDefense, interceptionTime: interceptionTime });
        }
    });

    if (interceptors.length > 0) {
        // Sort interceptors by fastest interception time
        interceptors.sort(function (a, b) {
            return a.interceptionTime - b.interceptionTime;
        });

        // Try interception by the fastest air defenses in order
        let INTERCEPTED = false;
        for (let i = 0; i < interceptors.length && !INTERCEPTED; i++) {
            let interceptor = interceptors[i];
            let airDefense = interceptor.airDefense;
            let interceptionTime = interceptor.interceptionTime;

            // Calculate time to impact (launcher missile)
            var timeToImpact = calculateTimeToImpact(closestLauncher.lat, closestLauncher.lng, targetLat, targetLng, closestLauncher.speed);

            // console.log(`Time to Impact: ${timeToImpact} seconds`);
            // console.log(`Interception Time: ${interceptionTime} seconds`);

            // Early detection bonus (reduce reaction time)
            var distanceToTarget = map.distance([airDefense.lat, airDefense.lng], [targetLat, targetLng]);
            if (distanceToTarget < airDefense.interceptionRange * 0.7) {
                interceptionTime *= 0.9; // 10% bonus for faster reaction
                console.log(`${airDefense.name} received an early detection bonus!`);
            }

            if(!defenseStatistics[airDefense]) {
                defenseStatistics[airDefense] = [];
                defenseStatistics[airDefense]['total'] = 0;
                defenseStatistics[airDefense]['success'] = 0;
                defenseStatistics[airDefense]['failure'] = 0; 
            }

            $.post('./scripts/airdefense_statistics.php', { id: airDefense.id, 'field': 'total' }, function() { });

            // Determine interception success based on accuracy
            var randomFactor = determineInterceptionSuccess(airDefense, timeToImpact, interceptionTime, distanceToTarget, closestLauncher);
            if (randomFactor && interceptionTime <= timeToImpact) {
                // Successful interception
                console.log(`${airDefense.name} intercepted the missile!`);
                defenseStatistics[airDefense]['success']++;

                $.post('./scripts/airdefense_statistics.php', { id: airDefense.id, 'field': 'success' }, function() { });

                // Calculate the interception point (based on interception speed and interception time)
                var interceptionPoint = calculateInterceptionPoint(closestLauncher.lat, closestLauncher.lng, targetLat, targetLng, interceptionTime, timeToImpact, airDefense);

                // Visual feedback: missile trail and interception marker
                var missileTrail = L.polyline([[airDefense.lat, airDefense.lng], interceptionPoint], {
                    color: 'gray',
                    weight: 2,
                    dashArray: '5, 10'
                }).addTo(map);

                var interceptionIcon = L.divIcon({
                    className: 'interception-icon',
                    html: '<i class="explosion">&#10041;</i>',
                    iconSize: [20, 20],
                    iconAnchor: [10, 20]
                });
                var interceptionMarker = L.marker(interceptionPoint, { icon: interceptionIcon }).addTo(map);

                // Show a tooltip with interception info
                interceptionMarker.bindTooltip(`
                    <b>${airDefense.name}</b> intercepted the rocket!<br>
                    Time to Impact: ${timeToImpact.toFixed(2)} sec<br>
                    Interception Time: ${interceptionTime.toFixed(2)} sec
                `, {
                    permanent: false,
                    direction: 'top',
                    offset: [0, -20]
                });

                INTERCEPTED = true;
            }

            if (!randomFactor) {
                console.log(`${airDefense.name} FAILED to intercept the missile!`);
                $.post('./scripts/airdefense_statistics.php', { id: airDefense.id, 'field': 'failure' }, function() { });
            }

        }

        if (!INTERCEPTED) {
            // Missile was not intercepted by any air defense, so add the blast to the map
            blastCircle.addTo(blastLayer);

            // Add hover info to blast circle
            blastCircle.bindTooltip(`
                <b>${closestLauncher.name}</b><br>
                Model: ${closestLauncher.model}<br>
                Blast Radius: ${parseFloat(closestLauncher.blastRadius).toFixed(2)} m
            `, {
                permanent: false,
                direction: 'top',
                offset: [0, -20]
            });
        }
    } else {
        // No air defenses in range, proceed with the blast
        blastCircle.addTo(blastLayer);
        blastCircle.bindTooltip(`
            <b>${closestLauncher.name}</b><br>
            Model: ${closestLauncher.model}<br>
            Blast Radius: ${parseFloat(closestLauncher.blastRadius).toFixed(2)} m
        `, {
            permanent: false,
            direction: 'top',
            offset: [0, -20]
        });
    }
});

function calculateInterceptionPoint(launcherLat, launcherLng, targetLat, targetLng, interceptionTime, timeToImpact, airDefense) {
    var totalDistance = map.distance([launcherLat, launcherLng], [targetLat, targetLng]);  // Total distance to target
    var interceptionDistance = (interceptionTime / timeToImpact) * totalDistance;  // Calculate interception distance

    // Convert interception range to float and constrain interception distance
    var interceptionRangeMeters = parseFloat(airDefense.interceptionRange);  // Interception range in meters

    // Ensure the interception distance doesn't exceed the interception range
    if (interceptionDistance > interceptionRangeMeters) {
        interceptionDistance = interceptionRangeMeters;  // Constrain interception distance to interception range
    }

    // If the total distance is zero, return the target point (edge case)
    if (totalDistance === 0) {
        return [targetLat, targetLng];
    }

    // Scale the interception distance and ensure it doesn't exceed interception range
    var factor = interceptionDistance / totalDistance;  // Factor to scale the lat/lng changes

    var interceptionLat = parseFloat(launcherLat) + (parseFloat(targetLat) - parseFloat(launcherLat)) * factor;
    var interceptionLng = parseFloat(launcherLng) + (parseFloat(targetLng) - parseFloat(launcherLng)) * factor;

    // Calculate the distance from air defense to interception point
    var distanceToInterception = map.distance([airDefense.lat, airDefense.lng], [interceptionLat, interceptionLng]);

    // If the calculated interception point is outside the interception range, adjust it to the edge of the interception range
    if (distanceToInterception > interceptionRangeMeters) {
        var scalingFactor = interceptionRangeMeters / distanceToInterception;  // Scale to bring within range
        interceptionLat = parseFloat(airDefense.lat) + (parseFloat(interceptionLat) - parseFloat(airDefense.lat)) * scalingFactor;
        interceptionLng = parseFloat(airDefense.lng) + (parseFloat(interceptionLng) - parseFloat(airDefense.lng)) * scalingFactor;
    }

    // Log for debugging
    // console.log(`Adjusted Interception Point: [${interceptionLat}, ${interceptionLng}]`);

    return [interceptionLat, interceptionLng];  // Return the adjusted interception point
}

// Function to calculate time to impact for launcher
function calculateTimeToImpact(launcherLat, launcherLng, targetLat, targetLng, launcherSpeed) {
    var distance = map.distance([launcherLat, launcherLng], [targetLat, targetLng]);  // Distance in meters
    return distance / launcherSpeed;  // Time = Distance / Speed (seconds)
}

// Function to calculate interception time for air defense
function calculateInterceptionTime(airDefense, targetLat, targetLng) {
    var distance = map.distance([airDefense.lat, airDefense.lng], [targetLat, targetLng]);  // Distance to blast point
    var reactionTime = parseFloat(airDefense.reactionTime);  // Reaction time in seconds
    var interceptionSpeed = parseFloat(airDefense.interceptionSpeed);  // Speed of intercepting rocket (m/s)

    if (!interceptionSpeed || interceptionSpeed <= 0) {
        console.error('Invalid interception speed for air defense:', airDefense.name);
        return Infinity;  // If no valid interception speed, interception is not possible
    }

    return reactionTime + (distance / interceptionSpeed);  // Total interception time = reaction time + travel time
}

// Add the layer control to the map
L.control.layers(null, {
    'Launchers & Ranges': launcherLayer,
    'Blasts': blastLayer,
    'Air Defenses': airDefenseLayer,
    'Ukraine War': ukraineWar,
    'BRICS': brics,
    'G7': g7,
    'G20': g20,
    'N.A.T.O.': nato,
    'Shanghai C.O.': shangai,
    'Five Eyes': fiveeyes,
    'E.U.': eu,
    'Euro Zone': eok,
    'WW2 Axis': ww2axis,
    'WW2 Allies': ww2allies,
    'Nuclear Reactors': nuclearFactoriesLayer
}, { position: 'topright', collapsed: false }).addTo(map);

// Function to update launcher position via AJAX
function updateLauncherPosition(id, newLat, newLng) {
    $.post('./scripts/update_launcher_coords.php', {
        id: id,
        lat: newLat,
        lng: newLng
    });
}

// Function to fill the form for editing a launcher
function fillLauncherForm(launcher) {
    // console.log(launcher)
    $('#launcherId').val(launcher.id);
    $('#template').val(launcher.templateID);
    $('#name').val(launcher.name);
    
    $('#lat').val(launcher.lat);
    $('#lng').val(launcher.lng);

    let description = '';
    if(launcher.description)
        description = launcher.description;

    $('#launcherdescription').text(description);
}

// Confirm deletion of launcher
$('#confirmDelete').click(function() {
    $.post('./scripts/delete_launcher.php', { id: selectedLauncherId }, function() {
        location.reload();
    });
});

// Confirm deletion of air-defense
$('#airconfirmDelete').click(function() {
    $.post('./scripts/delete_airdefense.php', { id: selectedLauncherId }, function() {
        location.reload();
    });
});

// Save new or updated launcher via AJAX
$('#saveLauncher').click(function() {
    var formData = new FormData(document.getElementById('launcherData'));
    var launcherId = $('#launcherId').val();

    // Append launcherId to the form data if we are updating
    if (launcherId) {
        formData.append('launcherId', launcherId);
    }

    var url = launcherId ? './scripts/update_launcher.php' : './scripts/save_launcher.php'; // Use update if launcherId exists

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                alert('Launcher was saved successfully!');
                // console.log(data.data);
                location.reload(); // Reload to display new or updated launcher
            } else {
                alert('Error during launcher creation.');
            }
        }
    });
});

// Track clicked position
var clickedLat = null;
var clickedLng = null;

// Left-click event to open selection modal
map.on('click', function(e) {
    // Store the clicked location
    clickedLat = e.latlng.lat;
    clickedLng = e.latlng.lng;

    // Open the selection modal
    $('#selectionModal').modal('show');
});

// Handle "Add Launcher" button click
document.getElementById('addLauncherBtn').onclick = function() {
    // Populate the hidden fields with the clicked coordinates
    $('#lat').val(clickedLat);
    $('#lng').val(clickedLng);
    $('#launcherId').val('');
    $('#template').val('');
    $('#name').val('');
    // Show the launcher modal
    $('#launcherModal').modal('show');

    // Hide the selection modal
    $('#selectionModal').modal('hide');
};

// Handle "Add Air Defense" button click
document.getElementById('addAirDefenseBtn').onclick = function(e) {
    $('#airDefenselat').val(clickedLat);
    $('#airDefenselng').val(clickedLng);
    $('#airDefenseId').val('');
    $('#airDefenseName').val('');
    $('#airDefenseTemplate').val('');
    
    $('#airDefenseModal').modal('show'); // Show modal for air defense
    map.closePopup(); // Close the context menu

    // Hide the selection modal
    $('#selectionModal').modal('hide');
};

// Handle air defense template selection and populate the name field
$('#airDefenseTemplate').on('change', function() {
    var templateId = $(this).val();

    if (templateId) {
        // Make an AJAX call to fetch the air defense template data
        $.ajax({
            url: './scripts/get_airdefense_template.php', // PHP script to fetch the template data
            type: 'POST',
            data: { templateId: templateId },
            success: function(response) {
                // Parse the JSON response
                var template = JSON.parse(response);
                $('#airDefenseName').val(template.name); // Auto-populate the name field
                $('#airdescription').text(template.description)
            }
        });
    } else {
        // Clear the form fields if no template is selected
        $('#airDefenseData')[0].reset();
    }
});

$('#saveAirDefense').click(function () {
    var formData = new FormData(document.getElementById('airDefenseData'));
    var airDefenseId = $('#airDefenseId').val();

    var url = './scripts/save_airdefense.php'; 

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                // console.log(data)
                alert('Air Defense saved successfully!');
                location.reload(); // Reload to display the updated air defenses
            } else {
                alert('Error saving air defense: ' + data.message);
            }
        }
    });
});

$('#airconfirmDelete').click(function () {
    $.post('./scripts/delete_airdefense.php', { id: selectedAirDefenseId }, function () {
        location.reload();
    });
});

function updateAirDefensePosition(id, newLat, newLng) {
    $.post('./scripts/update_airdefense_coords.php', {
        id: id,
        lat: newLat,
        lng: newLng
    });
}

function fillAirDefenseForm(airDefense) {
    // console.log(airDefense)
    $('#airDefenseId').val(airDefense.id);
    $('#airDefenseTemplate').val(airDefense.templateID);
    $('#airDefenseName').val(airDefense.name);
    $('#airDefenselat').val(airDefense.lat);
    $('#airDefenselng').val(airDefense.lng);

    $('#airdescription').text(airDefense.description);

    // Trigger the dropdown to update the template and other values
    $('#airDefenseTemplate').val(airDefense.templateID).trigger('change');
}

loadLaunchers(); // Load launchers on page load
loadAirDefenses();