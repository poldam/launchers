var google_id = null;
var temp_id = null;

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
    //$('#delayRange').val(delayTime);

    // add buttons to map
    if(google_id == null){ // Show button if not logged in
        L.control.LoginControl({ position: 'bottomright' }).addTo(map);
        $('#instructionsModal').modal('show'); // show the info modal
    }
    else
        L.control.LogoutControl({ position: 'bottomright' }).addTo(map); 

    // Load launchers 
    loadLaunchers(); 
    loadAirDefenses(); 
    renderProgressBar('progressBar', 0, 0);
});

// Initialize the map and markers
var map = L.map('map', {zoomControl: false, minZoom: 2}).setView([37.9838, 23.7275], 4); // Εστίαση στην Αθήνα

// Track clicked position
var clickedLat = null;
var clickedLng = null;

map.on('click', function(e) {
    clickedLat = e.latlng.lat;
    clickedLng = e.latlng.lng;

    // Open the selection modal
    $('#selectionModal').modal('show');
});

// marker icons
var launcherIcon = L.icon({
    iconUrl: './images/launcher-icon.png', 
    iconSize: [38, 38], 
    iconAnchor: [19, 38], 
    popupAnchor: [0, -40] 
});
var defenceIcon = L.icon({
    iconUrl: './images/airdefense-icon.png', 
    iconSize: [38, 38], 
    iconAnchor: [19, 38], 
    popupAnchor: [0, -40] 
});
var targetIcon = L.icon({
    iconUrl: './images/target-icon.png',
    iconSize: [20, 20]
});
var interceptionIcon = L.divIcon({
    className: 'interception-icon',
    html: '<i class="explosion">&#10041;</i>',
    iconSize: [20, 20],
    iconAnchor: [10, 20]
});

// Create separate layers for launchers, range circles, targets, blasts and interceptions
var launcherLayer = L.layerGroup().addTo(map);
var blastLayer = L.layerGroup().addTo(map);
var interceptionLayer = L.layerGroup().addTo(map);
var airDefenseLayer = L.layerGroup().addTo(map); 
var targetLayer = L.layerGroup().addTo(map);

var countryGroups = {};

var selectedLauncherId = null;
var currentMarker = null;
var rangeCircle = null;

var userMarker = null; // Store the user location marker
var userCircle = null; // Store the user location circle


/////////////////////////////
// Map Controls////////////
///////////////////////////
L.Control.mapControls = L.Control.extend({
    options: {
      position: 'bottomright'
    },
  
    onAdd: function () {
      var div = L.DomUtil.create('div', 'map-controls');
  
      div.innerHTML = `
        <div class="button-row clear-buttons">
            <button id="btn-clear-launchers">Clear Launchers</button>
            <button id="btn-clear-airdefenses">Clear Air Defenses</button>
            <button id="btn-clear-blasts">Clear Blasts</button>
            <button id="btn-clear-targets">Clear Targets</button>
        </div>
  
        <div class="button-row">
            <div class="button-row">
                <div class="simulate-btn">
                    <button id="simulateButton"class="btn btn-danger mb-0"><strong>Simulate!</strong></button>
                </div>
            </div> 
  
            <button id="settings-btn">
                <i class="fa-solid fa-circle-info" style="font-size: 25px;"></i>
            </button>
            <button id="location-btn">
                <i class="fa-solid fa-location-dot" style="font-size: 25px;"></i>
            </button>
        </div>
        <div id="progressBar" style="display: flex; height: 4px; gap: 2px; padding-top:7px;"></div>
      `;
//      <div class="slidecontainer" style="display: flex; align-items: center; gap: 5px; padding-bottom: 2px;"> 
//         <span style="font-weight: bold;">Delay</span>     
//         <input type="range" min="0" max="1000" step="100" value="0" id="delayRange" style="width: 100px; height: 5px;">
//         <span id="delayValue" style="font-weight: bold;">500</span> ms     
//     </div>
        
      return div;
    }
  });


  
  var mapControls = new L.Control.mapControls();
  map.addControl(mapControls);

  $(function () {
    $('#btn-clear-launchers').on('click', function (e) {
      e.stopPropagation();
      clearLaunchers();
    });
    $('#btn-clear-airdefenses').on('click', function (e) {
      e.stopPropagation();
      clearAirdefenses();
    });
    $('#btn-clear-blasts').on('click', function (e) {
      e.stopPropagation();
      clearBlasts();
    });
    $('#btn-clear-targets').on('click', function (e) {
      e.stopPropagation();
      clearTargets();
    });
    $('.simulate-btn button').on('click', function (e) {
      e.stopPropagation();
      simulation();
    });
    $('#settings-btn').on('click', function (e) {
      e.stopPropagation();
      $('#instructionsModal').modal('show');
    });
    $('#location-btn').on('click', function (e) {
      e.stopPropagation();
      findMyLocation();
    });
    $('.slidecontainer').on('mousedown touchstart pointerdown click', function (e) {
        e.stopPropagation();
        delayTime = $('#delayRange').val();
      });

      $('#delayRange').on('input', () => {
        $('#delayValue').text($('#delayRange').val());
    });
  });

// clear functions
function clearLaunchers() {
    launchers = [];
    launcherLayer.clearLayers();
    $.ajax({
        url: './scripts/delete_launchers.php',   
        method: 'POST',
        success: function(response) {
            console.log('Launchers cleared', response);
        },
        error: function(err) {
            console.error('Error clearing launchers', err);
        }
    });
    loadLaunchers();
}

function clearAirdefenses() {
    airDefenses = [];
    airDefenseLayer.clearLayers();
    $.ajax({
        url: './scripts/delete_airdefenses.php', 
        method: 'POST',
        success: function(response) {
            console.log('Airdefenses cleared', response);
        },
        error: function(err) {
            console.error('Error clearing airdefenses:', err);
        }
    });
    loadAirDefenses();
}

function clearTargets() {
    targets = []
    targetLayer.clearLayers();
}

function clearBlasts() {
  blastLayer.clearLayers();
  interceptionLayer.clearLayers();
}

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

// progress bar
function renderProgressBar(containerId, totalSlices, filledSlices) {
    
    const container = $('#' + containerId);
    totalSlices === 0 ? container.hide() : container.show();
    
    container.empty(); 
    for (let i = 0; i < totalSlices; i++) {
        const isFilled = i < filledSlices ? 'filled' : '';
        const slice = $('<div></div>')
            .addClass('progress-slice')
            .addClass(isFilled);
        container.append(slice);
    }
}
/////////////////////////////
// Google Login Button//////
///////////////////////////
L.Control.LoginControl = L.Control.extend({
    onAdd: function () {
    var div = L.DomUtil.create('div', 'google-btn-login');
    div.innerHTML = `
      <button class="google-btn" onclick="window.location.href='${window.loginUrl}'">
        <i class="fa-brands fa-google" style="margin-right: 6px;"></i>
        <span>Login with Google</span>
      </button>
    `;
    div.onclick = function(e) {
        clearAirdefenses();
        clearLaunchers();
        L.DomEvent.stopPropagation(e);
    };
    return div;
  }
});

L.control.LoginControl = function(opts) {
    return new L.Control.LoginControl(opts);
}

L.Control.LogoutControl = L.Control.extend({
    onAdd: function () {
    var div = L.DomUtil.create('div', 'google-btn-logout');
    div.innerHTML = `
      <button class="google-btn" onclick="window.location.href='./auth/logout.php'">
        <span>Logout</span>
      </button>
    `;
    div.onclick = function(e) {
        L.DomEvent.stopPropagation(e);
    };
    return div;
  }
});

L.control.LogoutControl = function(opts) {
    return new L.Control.LogoutControl(opts);
}

//////////////////////////////////////////////////
//////////////////////////////////////////////////

// Array to store launcher data
var launchers = [];
var airDefenses = [];
var detectedAirDefenses = [];
var defenseStatistics = [];
var targets = [];

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
        updateLauncherTargets(launcher.id);
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
        //dashArray: '5, 10', // Dashed line for interception range (Reeeeeeeeeeeeally slow when zooming in)
    }).addTo(airDefenseLayer);

    // Interception range circle 
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
        isHypersonicCapable: airDefense.isHypersonicCapable,
        numTrackedTargets: 0
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

function updateLauncherPosition(id, newLat, newLng) {
    $.post('./scripts/update_launcher_coords.php', {
        id: id,
        lat: newLat,
        lng: newLng
    });
}

function updateAirDefensePosition(id, newLat, newLng) {
    $.post('./scripts/update_airdefense_coords.php', {
        id: id,
        lat: newLat,
        lng: newLng
    });
}

// Function to update the launcher layer when adding or removing a launcher
function reloadLauncherLayer(){
    launcherLayer.clearLayers();
    launchers = [];
    loadLaunchers();
}

// Function to update the airdefense layer when adding or removing a launcher
function reloadAirdefenseLayer(){
    airDefenseLayer.clearLayers();
    airDefenses = [];
    loadAirDefenses();
}

function getAggressiveColor() {
    // Generate random aggressive colors with more emphasis on red and dark tones
    let r = Math.floor(Math.random() * 156 + 100); // Red channel - higher intensity
    let g = Math.floor(Math.random() * 100); // Green channel - low to medium intensity
    let b = Math.floor(Math.random() * 100); // Blue channel - low to medium intensity

    // Convert to hex and return the color
    return '#' + r.toString(16).padStart(2, '0') + g.toString(16).padStart(2, '0') + b.toString(16).padStart(2, '0');
}

function loadTargets(){
    targetLayer.clearLayers();
    targets.forEach(target => {
        addTargetToMap(target);
    });
}

function addTargetToMap(target){
    var marker = L.marker([target.lat, target.lng], { draggable: true, icon: targetIcon }).addTo(targetLayer);
    var launcher = launchers.find(launcher => launcher.id === target.launcherId)
    launcherId = launcher.id;
    var launcherName = launcher.name;
    marker.bindTooltip(
     `Launcher: ${launcherName}<br>
     Launch Time(s): ${target.launchTime.toFixed(2)}`, 
     {
         permanent: false, 
         direction: "top"
     }
     );
 
     // update target position on drag
     marker.on('dragend', function (e) {
         const newPos = e.target.getLatLng();
 
         const originalTarget = targets.find(t => t.id === target.id);
         
         if (originalTarget) {
             if(launcher.range >= map.distance([launcher.lat, launcher.lng], [newPos.lat, newPos.lng])){
                 originalTarget.lat = newPos.lat;
                 originalTarget.lng = newPos.lng;
             }else{ 
                 targets.pop(target); //if out of range remove it
                 map.removeLayer(marker);
             }
             //console.log(`Target ${originalTarget.id} moved to:`, newPos);
         }
     });
 
     // remove target on right click
     marker.on('contextmenu', function (e) {
         map.removeLayer(marker);
         const index = targets.findIndex(t => t.id === target.id);
         if (index !== -1) targets.splice(index, 1);
     });
 
}

// returns launchers in range of targets
function getLaunchersInRange(launchers, targetLat, targetLng) {
    let inRange = [];

    launchers.forEach(function (launcher) {
        const distance = map.distance([launcher.lat, launcher.lng], [targetLat, targetLng]);
        
        // Check if this launcher within range
        if (distance <= launcher.range) {
            launcher._distanceToTarget = distance;
            inRange.push(launcher);
        }
    });

    inRange.sort((a, b) => a._distanceToTarget - b._distanceToTarget);
    inRange.forEach(l => delete l._distanceToTarget);

    return inRange;
}
// delete targets that are out of range after moving a launcher
function updateLauncherTargets(launcherid){
     var launcher = launchers.find(l => l.id === launcherid);
     flag = false; // launcher too far from target
     for (var i = targets.length - 1; i >= 0; i--) {
         if (targets[i].launcherId === launcherid) {
             const distance = map.distance([launcher.lat, launcher.lng], [targets[i].lat, targets[i].lng]);
 
             if (launcher.range <= distance) {
                 targets.splice(i, 1); 
                 flag = true;
             } else {
                 targets[i].timeToImpact = calculateTimeToImpact(
                     launcher.lat, launcher.lng,
                     targets[i].lat, targets[i].lng,
                     launcher.speed
                 );
             }
         }
     }
     if(flag)
         loadTargets();
}
  
function updateTrackedTargets(targets, airdefense, time){
     for(const target of targets){
         if(target.launchTime <= time && target.timeToImpact > time && targetInRange(target, airdefense))
             airdefense.numTrackedTargets++;
     }
     console.log(airdefense.numTrackedTargets);
}
 
function targetInRange(target, airdefense){
     return map.distance([airdefense.lat, airdefense.lng], [target.lat, target.lng]) <= airdefense.interceptionRange;
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

// Simulate interception success based on multiple realistic factors
function determineInterceptionSuccess(airDefense, timeToImpact, interceptionTime, distanceToTarget, launcher) {
    var randomFactor = Math.random();
    
    // Factor 1: Speed of the incoming missile vs air defense interception speed
    const HYPERSONIC = 1715, SUPERSONIC = 412; // Launcher speeds in m/s
    
    var missileSpeedRatio = launcher.speed / airDefense.interceptionSpeed; // Ratio of missile speed to interception speed
    var speedPenalty = 0.2 * (missileSpeedRatio - 1); // Gradual penalty for faster missiles
    var speedFactor;
    
    if (launcher.speed >= HYPERSONIC) {
        speedFactor = airDefense.isHypersonicCapable ? 1 - speedPenalty : 0.1; // Hypersonic missiles (e.g., Kinzhal) are nearly impossible to intercept unless countered by hypersonic systems
    } else if (launcher.speed >= SUPERSONIC) {
        speedFactor = 1 - speedPenalty; 
    } else {
        speedFactor = 1.0; // No penalty for slower missiles
    }
     
    // Factor 2: Early detection bonus - higher success if detected early
    var earlyDetectionBonus = (distanceToTarget < airDefense.interceptionRange * 0.7) ? 1.1 : 1.0; // 10% bonus for early detection

    // Factor 3: Multiple targets - degrade effectiveness if multiple missiles are incoming
    var targetOverloadFactor = airDefense.numTrackedTargets > airDefense.maxSimultaneousTargets
    ? 0.8 * Math.exp(-(airDefense.numTrackedTargets - airDefense.maxSimultaneousTargets) / 5)  
    : 1.0; // decreases exponentially as the number of targets rises

    // Factor 4: Environmental randomness - simulating interference or weather
    var environmentFactor = 1 - (Math.random() * 0.05); // Small random degradation (e.g., jamming, fog)

    // Factor 5: Hypersonic countermeasures
    var hypersonicBonus = launcher.speed < HYPERSONIC && airDefense.isHypersonicCapable ? 1.2 : 1.0; // Boost if air defense can handle hypersonics

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

function calculateInterceptionPoint(launcherLat, launcherLng, targetLat, targetLng, interceptionTime, timeToImpact, airDefense) {
    var totalDistance = map.distance([launcherLat, launcherLng], [targetLat, targetLng]);  
    var interceptionDistance = (interceptionTime / timeToImpact) * totalDistance; 

    var interceptionRangeMeters = parseFloat(airDefense.interceptionRange);  

    if (interceptionDistance > interceptionRangeMeters) {
        interceptionDistance = interceptionRangeMeters;  
    }

    if (totalDistance === 0) {
        return [targetLat, targetLng];
    }

    var factor = interceptionDistance / totalDistance; 

    var interceptionLat = parseFloat(launcherLat) + (parseFloat(targetLat) - parseFloat(launcherLat)) * factor;
    var interceptionLng = parseFloat(launcherLng) + (parseFloat(targetLng) - parseFloat(launcherLng)) * factor;

    var distanceToInterception = map.distance([airDefense.lat, airDefense.lng], [interceptionLat, interceptionLng]);

    if (distanceToInterception > interceptionRangeMeters) {
        var scalingFactor = interceptionRangeMeters / distanceToInterception;  // Scale to bring within range
        interceptionLat = parseFloat(airDefense.lat) + (parseFloat(interceptionLat) - parseFloat(airDefense.lat)) * scalingFactor;
        interceptionLng = parseFloat(airDefense.lng) + (parseFloat(interceptionLng) - parseFloat(airDefense.lng)) * scalingFactor;
    }

    // Log for debugging
    // console.log(`Adjusted Interception Point: [${interceptionLat}, ${interceptionLng}]`);

    return [interceptionLat, interceptionLng];  // Return the adjusted interception point
}

function calculateTimeToImpact(launcherLat, launcherLng, targetLat, targetLng, launcherSpeed) {
    var distance = map.distance([launcherLat, launcherLng], [targetLat, targetLng]);  // Distance in meters
    return distance / launcherSpeed;  // Time = Distance / Speed (seconds)
}

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

function delay(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function simulation() {
    $('#simulateButton').prop('disabled', true);
    let count = 0;
    renderProgressBar('progressBar', targets.length, count);
    if (targets.length === 0) {
        $('#simulateButton').prop('disabled', false);
        return;
    }

    // sort by launchTime ascending
    targets.sort((a, b) => a.launchTime - b.launchTime);

    // track time
    let simTime = 0;
    let previousTime = 0;
    for (const target of targets) {
        simTime = target.launchTime;

        if (previousTime != simTime) {
            await delay(simTime - previousTime);
        }
        simTarget(target, simTime);
        previousTime = simTime;

        renderProgressBar('progressBar', targets.length, ++count);
    }

    // reset tracked targets
    for (const airdefense of detectedAirDefenses)
        airdefense.numTrackedTargets = 0;

    $('#simulateButton').prop('disabled', false);
    return;
}

function simTarget(target, time) {
    const launcher = launchers.find(l => l.id === target.launcherId);
    const lat = parseFloat(target.lat), lng = parseFloat(target.lng);

    console.log(`\n time=${time.toFixed(2)}s: launching ${launcher.name}, at [${lat.toFixed(2)},${lng.toFixed(2)}]`);

    detectedAirDefenses = [];
    // collect possible interceptors
    let interceptors = [];
    let intercepted = false;

    airDefenses.forEach(airdefense => {
        const dist = map.distance([airdefense.lat, airdefense.lng], [lat, lng]);

        if (dist <= airdefense.detectionRange) {
            console.log(`${airdefense.name} detected incoming missile`);
            detectedAirDefenses.push(airdefense);
        }
        if (dist <= airdefense.interceptionRange) {
            let interceptionTime = calculateInterceptionTime(airdefense, lat, lng) + time;
            interceptors.push({ airdefense: airdefense, interceptionTime: interceptionTime });
        }
    });
    
    if (interceptors.length > 0) {
        interceptors.sort((x, y) => x.interceptionTime - y.interceptionTime);

        // Try intercepting with each airdefense
        for (let i = 0; i < interceptors.length && !intercepted; i++) {
            let interceptor = interceptors[i];
            let interceptionTime = interceptor.interceptionTime;

            let airdefense = interceptor.airdefense; // intercepting airdefense
            var timeToImpact = calculateTimeToImpact(launcher.lat, launcher.lng, lat, lng, launcher.speed) + time; // Calculate time to impact (launcher missile)
            
            // get number of targets tracked according to time.
            updateTrackedTargets(targets, airdefense, time); // tracks only if inside interception range

            var distanceToTarget = map.distance([parseFloat(airdefense.lat), parseFloat(airdefense.lng)], [lat, lng]);
            
            console.log(`Time to Impact: ${timeToImpact.toFixed(2)} seconds`);
            console.log(`Interception Time: ${interceptionTime.toFixed(2)} seconds`);

            if(!defenseStatistics[airdefense]) {
                defenseStatistics[airdefense] = [];
                defenseStatistics[airdefense]['total'] = 0;
                defenseStatistics[airdefense]['success'] = 0;
                defenseStatistics[airdefense]['failure'] = 0; 
            }
            $.post('./scripts/airdefense_statistics.php', { id: airdefense.id, 'field': 'total' }, function() { });

            // Determine interception success based on accuracy
            var success = determineInterceptionSuccess(airdefense, timeToImpact, interceptionTime, distanceToTarget, launcher);

            // Successful interception
            if(success){   
                console.log(`${airdefense.name} intercepted the missile!`);
                
                defenseStatistics[airdefense]['success']++;
                $.post('./scripts/airdefense_statistics.php', { id: airdefense.id, 'field': 'success' }, function() { });

                //Calculate the interception point (based on interception speed and interception time)
                var interceptionPoint = calculateInterceptionPoint(launcher.lat, launcher.lng, lat, lng, interceptionTime, timeToImpact, airdefense);
                var missileTrail = L.polyline([[airdefense.lat, airdefense.lng], interceptionPoint], {
                    color: 'gray',
                    weight: 2,
                    dashArray: '5, 10'
                }).addTo(interceptionLayer);
                var interceptionMarker = L.marker(interceptionPoint, { icon: interceptionIcon }).addTo(interceptionLayer);
                interceptionMarker.bindTooltip(`
                    <b>${airdefense.name}</b> intercepted the rocket!<br>
                    Time to Impact: ${timeToImpact.toFixed(2)} sec<br>
                    Interception Time: ${interceptionTime.toFixed(2)} sec
                `, {
                    permanent: false,
                    direction: 'top',
                    offset: [0, -20]
                });

                intercepted = true;
            }

            if (!success) {
                console.log(`${airdefense.name} FAILED to intercept the missile!`);
                $.post('./scripts/airdefense_statistics.php', { id: airdefense.id, 'field': 'failure' }, function() { });
            }

        }
    }
    
    // create the blast circle`
    if (!intercepted) {     
        var blastCircle = L.circle([lat, lng], {
            radius: launcher.blastRadius,
            color: launcher.color,
            fillColor: 'orange',
            fillOpacity: 1,
            weight: 6
        }).addTo(blastLayer).bindTooltip
        (
            `<b>${launcher.name}</b><br>
            Model: ${launcher.model}<br>
            Blast Radius: ${parseFloat(launcher.blastRadius).toFixed(2)} m`, 
        {
            permanent: false,
            direction: 'top',
            offset: [0, -20]
        });

        // trajectory line
        const launcherCoords = [launcher.lng, launcher.lat];
        const targetCoords = [target.lng, target.lat];

        const circleLine = turf.greatCircle(
            turf.point(launcherCoords),
            turf.point(targetCoords),
            { npoints: 100 }
        );
        const trajectory = L.geoJSON(circleLine, {
        style: {
            color: 'gray',
            weight: 2,
            dashArray: '5, 10'
        }
        })
        // show on hover
        blastCircle.on('mouseover', () => {
            trajectory.addTo(map);
        });
        blastCircle.on('mouseout', () => {
            map.removeLayer(trajectory);
        });
    }

    for(let i = 0; i < interceptors.length; i++){
        interceptors[i].airdefense.numTrackedTargets = 0;
    }
        
}

//////////////////
///// Modals /////
//////////////////

///// Launchers /////
// Handle "Add Offense" button click
$('#addLauncherBtn').on('click', function() {
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
});

// save 
$('#saveLauncher').click(function() {
    if ($(this).prop('disabled')) return; 
    var $button = $(this); 
    $button.prop('disabled', true);

    var formData = new FormData(document.getElementById('launcherData'));
    var launcherId = $('#launcherId').val();

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
                // console.log(data.data);
                reloadLauncherLayer(); // Refresh layer
                $('#launcherModal').modal('hide');    
            } else {
                alert('Error during launcher creation.');
            }
        },
        complete: function () { $button.prop('disabled', false); }
    });
    
});

// delete 
$('#confirmDelete').click(function() {
    var $button = $(this); 
    if ($button.prop('disabled')) return; 
    $button.prop('disabled', true); 

    $.post('./scripts/delete_launcher.php', { id: selectedLauncherId }, function() {
        $('#deleteModal').modal('hide');
        reloadLauncherLayer();
        $button.prop('disabled', false);     
    });

    // delete targets related to launcher
    for (let i = 0; i < targets.length; i++) {
        if (targets[i].launcherId == selectedLauncherId) {
            targets.splice(i, 1);
        }
    }
    loadTargets();
});

// Function to fill the form for editing a launcher
function fillLauncherForm(launcher) {
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

///// Air Defences /////

// Handle "Add Defense" button click 
$('#addAirDefenseBtn').on('click', function(e) {
    $('#airDefenselat').val(clickedLat);
    $('#airDefenselng').val(clickedLng);
    $('#airDefenseId').val('');
    $('#airDefenseName').val('');
    $('#airDefenseTemplate').val('');
    
    $('#airDefenseModal').modal('show'); // Show modal for air defense
    map.closePopup(); // Close the context menu

    // Hide the selection modal
    $('#selectionModal').modal('hide');
});

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

// save
$('#saveAirDefense').click(function () {
    if ($(this).prop('disabled')) return; 
    var $button = $(this); 
    $button.prop('disabled', true);

    var formData = new FormData(document.getElementById('airDefenseData'));
    var airdefenseId = $('#airDefenseId').val();

    var url = airdefenseId ? './scripts/update_airdefense.php' : './scripts/save_airdefense.php'; 

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
                reloadAirdefenseLayer();
                $('#airDefenseModal').modal('hide');
            } else {
                alert('Error saving air defense: ' + data.message);
            }
        },
        complete: function () { $button.prop('disabled', false); }
    });
});

// delete air defense button
$('#airconfirmDelete').click(function () {
    var $button = $(this); 
    if ($button.prop('disabled')) return; 
    $button.prop('disabled', true); 
    $.post('./scripts/delete_airdefense.php', { id: selectedAirDefenseId }, function () {
        reloadAirdefenseLayer();
        $('#airdeleteModal').modal('hide');
        $button.prop('disabled', false); 
        
    });
});

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

///// Blasts /////

// Right-click to open modal to select launcher for the blast
map.on('contextmenu', function (e) {
    clickedLat = e.latlng.lat;
    clickedLng = e.latlng.lng;
    launchersInRange = getLaunchersInRange(launchers, clickedLat, clickedLng);
   
    if(launchersInRange.length === 0){
        alert('No launchers in range!');
        return;
    }

    const dropdown = $('#blastLauncher');
    dropdown.empty(); 

    launchersInRange.forEach((launcher, index) => {
        $('<option></option>') 
            .val(launcher.id) 
            .text(launcher.name)
            .prop('selected', index === 0)  
            .appendTo(dropdown)
    });

    $('#lat').val(clickedLat);
    $('#lng').val(clickedLng);
    $('#blastSelectionModal').modal('show');
});

let targetId = 0; // id of target
$('#saveBlast').click(function () {  
    let selectedLauncherId = $('#blastLauncher').val();
    //var launcher = launchers.find(launcher => launcher.id === parseInt(selectedLauncherId))
    let lat = $('#lat').val();
    let lng = $('#lng').val();
    let launchTime = $('#launchTime').val();

    if (!selectedLauncherId) {
        alert("Please select a launcher.");
        return;
    }

    targets.push({
        id : targetId,
        launcherId: parseInt(selectedLauncherId),
        lat: parseFloat(lat),
        lng: parseFloat(lng),
        launchTime: parseInt(launchTime),  // in seconds
    });
    targetId++;

    let target = targets.slice(-1)[0];
    addTargetToMap(target);
    renderProgressBar('progressBar', targets.length, 0);
    $('#blastSelectionModal').modal('hide');
});

$('#fire-now').click(function () {   
    let selectedLauncherId = $('#blastLauncher').val();
    //var launcher = launchers.find(launcher => launcher.id === parseInt(selectedLauncherId))
    let lat = $('#lat').val();
    let lng = $('#lng').val();
    let launchTime = $('#launchTime').val();

    if (!selectedLauncherId) {
        alert("Please select a launcher.");
        return;
    }

    targets.push({
        id : targetId,
        launcherId: parseInt(selectedLauncherId),
        lat: parseFloat(lat),
        lng: parseFloat(lng),
        launchTime: parseInt(launchTime),  // in seconds
    });
    targetId++;

    let target = targets.pop();
    simTarget(target,0);
    
    $('#blastSelectionModal').modal('hide');
});

// On page load
$.getJSON("./scripts/get_session_data.php", function(data) {
    // Get session data
    google_id = data['google_id']; 
    temp_id = data['temp_id'];    
});

window.addEventListener("beforeunload", function(event) {
    if(!google_id){
        navigator.sendBeacon("./scripts/delete_launchers.php");
        navigator.sendBeacon("./scripts/delete_airdefenses.php");
    }
});