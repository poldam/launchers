//////// Maps //////////
let osm = L.tileLayer ('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: ''
}).addTo (map);

// Streets
let googleStreets = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',{
    maxZoom: 20,
    subdomains:['mt0','mt1','mt2','mt3']
});

// Hybrid:
let googleHybrid = L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}',{
    maxZoom: 20,
    subdomains:['mt0','mt1','mt2','mt3']
});

// Satellite:
let googleSat = L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',{
    maxZoom: 20,
    subdomains:['mt0','mt1','mt2','mt3']
});

// Terrain
let googleTerrain = L.tileLayer('http://{s}.google.com/vt/lyrs=p&x={x}&y={y}&z={z}',{
    maxZoom: 20,
    subdomains:['mt0','mt1','mt2','mt3']
});

let cartoDBPositron = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    attribution: '',
    subdomains: 'abcd',
    maxZoom: 19
});

let cartoDBDarkMatter = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
    attribution: '',
    subdomains: 'abcd',
    maxZoom: 19
});

let powerGridLayer = L.tileLayer('https://tiles.openinframap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: ''
})

let flosmPowerGridLayer = L.tileLayer('https://{s}.flosm.de/power/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: ''
})

let osmPowerLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: ''
})

const baseMaps = {
    "OSM": osm,
    "Streets": googleStreets,
    "Satelite": googleSat,
    "Hybrid": googleHybrid,
    "Terain": googleTerrain,
    "Bright": cartoDBPositron,
    "Dark Matter": cartoDBDarkMatter
};

const controlMaps = L.control.layers(baseMaps, null, { position: 'topright', collapsed: true }).addTo(map);

//////// Country Groups //////////
countryGroups.ukraineWar = L.layerGroup();
countryGroups.brics = L.layerGroup();
countryGroups.g7 = L.layerGroup();
countryGroups.g20 = L.layerGroup();
countryGroups.nato = L.layerGroup();
countryGroups.shangai = L.layerGroup();
countryGroups.ww2axis = L.layerGroup();
countryGroups.ww2allies = L.layerGroup();
countryGroups.fiveeyes = L.layerGroup();
countryGroups.eu = L.layerGroup();
countryGroups.eok = L.layerGroup();

// Fetch the GeoJSON data for countries
let myjson = null;
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
            '#008f00', countryGroups.brics
        );

        // Add BRICS+ countries
        addCountriesToLayerGroup(
            ['Iran', 'Ethiopia', 'Saudi Arabia', 'United Arab Emirates', 'Egypt'],
            '#adff2f', countryGroups.brics
        );

        // Add G7 countries
        addCountriesToLayerGroup(
            ['United States of America', 'Canada', 'Germany', 'Japan', 'United Kingdom', 'Italy', 'France'],
            '#ff0000', countryGroups.g7
        );

        // UkraineWar
        addCountriesToLayerGroup(
            ['Russia'],
            '#0000FF', countryGroups.ukraineWar
        );
        addCountriesToLayerGroup(
            ['Ukraine'],
            '#FFFF00', countryGroups.ukraineWar
        );
        addCountriesToLayerGroup(
            ['Finland','Georgia','Moldova','Estonia','Latvia','Lithuania'],
            '#555555', countryGroups.ukraineWar
        );
        addCountriesToLayerGroup(
            ['Belarus'],
            '#87CEEB', countryGroups.ukraineWar
        );

        addCountriesToLayerGroup(
            ['Australia','Canada','Saudi Arabia','United States of America','India','Russia','South Africa','Turkey','Argentina','Brazil','Mexico','France','Germany','Italy','United Kingdom','China','Indonesia','Japan','South Korea'],
            '#FFD700', countryGroups.g20
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
            '#004990', countryGroups.nato
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
            '#C1E4D0', countryGroups.shangai
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
            '#ff0000', countryGroups.ww2axis
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
            '#0033A0', countryGroups.fiveeyes
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
            '#012169', countryGroups.ww2allies
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
            '#0034A0', countryGroups.eu
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
            '#0047A0', countryGroups.eok
        );
          
    })
    .catch(error => console.error("Error fetching or processing GeoJSON data:", error));

//////// Nuclear Sites //////////
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

const controlLayers = L.control.layers(null, {
    'Launchers & Ranges': launcherLayer,
    'Blasts': blastLayer,
    'Interceptions': interceptionLayer,
    'Air Defenses': airDefenseLayer,
    'Ukraine War': countryGroups.ukraineWar,
    'BRICS': countryGroups.brics,
    'G7': countryGroups.g7,
    'G20': countryGroups.g20,
    'N.A.T.O.': countryGroups.nato,
    'Shanghai C.O.': countryGroups.shangai,
    'Five Eyes': countryGroups.fiveeyes,
    'E.U.': countryGroups.eu,
    'Euro Zone': countryGroups.eok,
    'WW2 Axis': countryGroups.ww2axis,
    'WW2 Allies': countryGroups.ww2allies,
    'Nuclear Reactors': nuclearFactoriesLayer
}, { position: 'topright', collapsed: true }).addTo(map);

// for custom css on the collapsed icon
const mapsContainer = controlMaps.getContainer();
mapsContainer.classList.add('maps-control');

// const layersContainer = controlMap.getContainer();
// control2Container.classList.add('layers-control');




