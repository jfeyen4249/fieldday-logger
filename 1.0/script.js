var map = L.map('map').setView([30.566793, -0.678554],2);
 L.tileLayer('https://api.maptiler.com/maps/streets/{z}/{x}/{y}.png?key=VRriVCRQ5SeLPdIdNuzi', {
     attributions: '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>'
 }).addTo(map);

var layerGroup = L.layerGroup().addTo(map);
getStats();
loggerStatus();
//checkContact();
let calllat = ''; 
let calllng = '';
let callcountry = ''; 
let callname = ''; 

function test(){
   const callsign = document.getElementById('input').innerHTML
   const callclass = document.getElementById('class').innerText
   const callsection = document.getElementById('section').innerText

   const band = document.getElementById('band').innerText
   const contactmode = document.getElementById('mode').innerText
   const operator = document.getElementById('operator').innerText

   console.log(callsign);
}


function loggerStatus() {
    document.getElementById('logbtn').disabled = true;
};

function logContact() {
    
};

function errorcolors() {
    document.getElementById("input").style.backgroundColor = "Red";
    document.getElementById("input").style.color = "White";
};

function noerrorcolors() {
    document.getElementById("input").style.backgroundColor = "White";
    document.getElementById("input").style.color = "Black";
};

function validcall() {
    document.getElementById("input").style.backgroundColor = "Green";
    document.getElementById("input").style.color = "White";
    
};

function toolong() {
    document.getElementById("input").style.backgroundColor = "Yellow";
    document.getElementById("input").style.color = "Black";
    document.getElementById("result").innerHTML = 'No data found for this callsign';
};

function addContact() {

   //layerGroup.clearLayers();
   const callsign = document.getElementById('input').value
   console.log(callsign);
   const callclass = document.getElementById('class').value
   const callsection = document.getElementById('section').value

   const band = document.getElementById('band').value
   const contactmode = document.getElementById('mode').value
   const operator = document.getElementById('operator').value


   let myurl = 'https://joefeyen.com/dbq/api.php?api=logcontact&callsign=' + callsign + '&class=' + callclass +'&section=' + callsection +'&band=' + band +'&mode=' + contactmode + '&operator=' + operator + '&lat=' + calllat + '&lng=' + calllat + '&name=' + callname
   console.log(myurl);
    fetch(myurl)
        .then(response => response.text)
        .then(data => {
            console.log(data);
            getStats();
        });
};

function clearData() {
    document.getElementById("input").value = '';
    document.getElementById("section").value = '';
    document.getElementById("class").value = '';
    noerrorcolors();
    layerGroup.clearLayers();
    document.getElementById("result").innerHTML = '&nbsp;';
    document.getElementById("class").style.backgroundColor = "White";
    document.getElementById("class").style.color = "Black";
    document.getElementById("section").style.backgroundColor = "White";
    document.getElementById("section").style.color = "Black";
    map.setView([30.566793, -0.678554],2);
    document.getElementById('logbtn').disabled = true;
};

function checkClass() {
    const classText = document.getElementById("class").value.toUpperCase();
    let callClass = /([0-9])([0-9])?([A-Z])/.test(classText);
    if(callClass == true) {
        document.getElementById("class").style.backgroundColor = "Green";
        document.getElementById("class").style.color = "White";
    } else {
        document.getElementById("class").style.backgroundColor = "Yellow";
        document.getElementById("class").style.color = "Black";
    };

    if (callClass === '') {
        document.getElementById("class").style.backgroundColor = "White";
        document.getElementById("class").style.color = "Black";
    };
};

function checkSection() {
    const sectionText = document.getElementById("section").value.toUpperCase();
    let callsection = /([A-Z])([A-Z])([A-Z])?/.test(sectionText);
   
    if(callsection === true) {
        document.getElementById("section").style.backgroundColor = "Green";
        document.getElementById("section").style.color = "White";
    } else {
        document.getElementById("section").style.backgroundColor = "Yellow";
        document.getElementById("section").style.color = "Black";
    };

    if (callsection === '') {
        document.getElementById("section").style.backgroundColor = "White";
        document.getElementById("section").style.color = "Black";
    } else {  
    };
};

function search() {
    let x = document.getElementById("input").value;
    x = x.toUpperCase();

    let call1x1 = /([A-Z])([0-9])([A-Z])/.test(x);
    let call1x2 = /([A-Z])([0-9])([A-Z])([A-Z])/.test(x);
    let call1x3 = /([A-Z])([0-9])([A-Z])([A-Z])([A-Z])/.test(x);

    let call2x1 = /([A-Z])([A-Z])([A-Z])([0-9])/.test(x);
    let call2x2 = /([A-Z])([A-Z])([A-Z])([0-9])([A-Z])/.test(x);
    let call2x3 = /([A-Z])([A-Z])([0-9])([A-Z])([A-Z])([A-Z])/.test(x);

        if(call1x1 || call1x2 || call1x3 || call2x1 || call2x2 || call2x3 ===  true) {
            const band = document.getElementById("band").value;
            const mode = document.getElementById("mode").value;
            fetch(`https://joefeyen.com/dbq/api.php?api=check&callsign=${x}&band=${band}&mode=${mode}`)
            .then(response => response.json())
            .then(data => {
                const lat = data.lat;
                const lon = data.lon;
                const country = data.country;
                const fullname = data.name;
                const state = data.state;
                const status = data.status;
                if(status === "New Contact") {
                    if(country === 'United States') {
                        document.getElementById("section").value = state;
                        document.getElementById("result").innerHTML = fullname;
                        checkSection();
                        validcall();
                        map.setView([lat,lon],6);
                        document.getElementById('logbtn').disabled = false;
                    }else {
                        document.getElementById("section").value = "DX";
                        document.getElementById("input").style.backgroundColor = "Orange";
                        document.getElementById("input").style.color = "White";
                        document.getElementById("section").style.backgroundColor = "Orange";
                        document.getElementById("section").style.color = "White";
                        map.setView([lat,lon],5);
                        document.getElementById('logbtn').disabled = false;
                    }
                    addToMap(lat,lon,x,fullname)
                    contactlat = lat;
                    contactlng = lon;
                    contactname = fullname;
                    

                } else if (status === "Duplicate Contact") {
                    document.getElementById("result").innerHTML =`Duplicate Contact!`;
                    document.getElementById('logbtn').disabled = true;
                    document.getElementById("input").style.backgroundColor = "Black";
                    document.getElementById("input").style.color = "White";

                    document.getElementById("class").style.backgroundColor = "Black";
                    document.getElementById("class").style.color = "White";

                    document.getElementById("section").style.backgroundColor = "Black";
                    document.getElementById("section").style.color = "White";
                }
            })
            .catch(function() {
                layerGroup.clearLayers();
                document.getElementById("section").value = '';
                errorcolors();
                document.getElementById('logbtn').disabled = true;
            });   
        } else {
            
        layerGroup.clearLayers();
        noerrorcolors();
        }
        if(x.length <= 3) {
            document.getElementById("section").value = '';
            //document.getElementById("class").value = '';
            noerrorcolors();
            layerGroup.clearLayers();
            document.getElementById("result").innerHTML = '';
            document.getElementById("section").style.backgroundColor = "White";
            document.getElementById("section").style.color = "Black";
            map.setView([30.566793, -0.678554],2);
            document.getElementById('logbtn').disabled = true;
            document.getElementById("class").style.backgroundColor = "White";
            document.getElementById("class").style.color = "Black";

        }
        
};

function addToMap(lat,lon,callsign,name) {
    layerGroup.clearLayers();
    var marker = L.marker([lat, lon],{
        title: `${callsign} \n\n ${name}`
    }).addTo(layerGroup);


};

function getStats() {
    fetch(`https://joefeyen.com/dbq/api.php?api=counts`)
        .then(response => response.json())
        .then(data => {
            document.getElementById("stats").innerHTML = `Total Contacts: ${data.count} | SSB: ${data.ssb} | CW: ${data.cw} | Digital: ${data.digital}`;
        });
};

function qrzSearch(callsign) {
    fetch('https://xmldata.qrz.com/xml/current/?username=kd9hae;password=ArmY1234$$')
            .then (function(resp) {
                return resp.text();
            })
            .then(function(data) {
                let parser = new DOMParser(),
                xmlDoc = parser.parseFromString(data, 'text/xml');
                //console.log(data);
                const mykey = xmlDoc.getElementsByTagName('Key')[0].innerHTML;

                let URL = `https://xmldata.qrz.com/xml/current/?s=${mykey};callsign=${callsign}`
                //console.log(URL);
                fetch(URL)
                .then (function(resp) {
                    return resp.text();
                })
                .then(function(data) {
                   
                    if(country === 'United States') {
                        const section = xmlDoc.getElementsByTagName('state')[0].innerHTML;
                        document.getElementById("section").value = section;
                        checkSection();
                        validcall();
                        map.setView([lat,lon],6);
                        document.getElementById('logbtn').disabled = false;
                    }else {
                        document.getElementById("section").value = country;
                        document.getElementById("input").style.backgroundColor = "Orange";
                        document.getElementById("input").style.color = "White";
                        document.getElementById("section").style.backgroundColor = "Orange";
                        document.getElementById("section").style.color = "White";
                        map.setView([lat,lon],5);
                        document.getElementById('logbtn').disabled = false;
                    }
                    addToMap(lat,lon,callsign,fullname)
                    checkContact();
                })
                .catch(function() {
                    layerGroup.clearLayers();
                    document.getElementById("section").value = '';
                    errorcolors();
                    document.getElementById('logbtn').disabled = true;
                });   
            })
};
