// chart = new Chart(null, {
//     type: 'bar',
//     data: {
//         labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu' , 'Minggu'],
//         datasets: [
//             {
//                 label: 'Organik',
//                 data: Data.dailyAverage.organik,
//                 borderWidth: 0,
//                 backgroundColor: '#1c9939',
//                 borderRadius: 20
//             },
//             {
//                 label: 'Anorganik',
//                 data: Data.dailyAverage.anorganik,
//                 borderWidth: 0,
//                 backgroundColor: '#afb922',
//                 borderRadius: 20
//             }
//         ]
//     },

//     options: {
//         scales: {
//             y: {
//                 beginAtZero: true
//             }
//         },
//         responsive:true,
//         maintainAspectRatio: false,
//         scales: {
//             yAxes: [{
//                 ticks: {
//                     beginAtZero:true
//                 }
//             }]
//         }
//     }
// });

// var ctx = document.getElementById('myChart');

var Data = {
    dailyAverage: {
        organik: [0, 0, 0, 0, 0, 0, 0],
        anorganik: [0, 0, 0, 0, 0, 0, 0]
    },
    current: {
        organik: 30,
        anorganik: 68
    }
}

var StatusElem = {
    organik: document.getElementById('organik'),
    anorganik: document.getElementById('anorganik')
}

function openTab(id) {
    var tabs = document.querySelectorAll(".tab");
    for (var i = 0; i < tabs.length; i++) {
        if (tabs[i].id != id) {
            tabs[i].hidden = true;
        }
        else {
            tabs[i].hidden = false;
        }
    }
}

openTab('home');

function dataSmartBin() {
    var http = new XMLHttpRequest();
    // var form = new FormData();
    http.open("GET", "/smartbinstatus.php");

    //Send the proper header information along with the request
    // http.setRequestHeader('Content-type', 'text/plain');

    http.onreadystatechange = function() {//Call a function when the state changes.
        if (http.readyState == 4 && http.status == 200) {
            const smartbin_state = JSON.parse(this.responseText);
            // console.log("Data received: ");
            // console.log(server_data);
            Data.current.organik = Number(smartbin_state.organic);
            Data.current.anorganik = Number(smartbin_state.anorganic);
            // alert("Data received!");
            loadDataSmartBin();
            // update_data();
            // Server.lastUpdate = (new Date()).getTime();;
        }
    }
    http.send();
}

function dataWeek() {
    var http = new XMLHttpRequest();
    // var form = new FormData();
    http.open("GET", "/data_week.php");

    //Send the proper header information along with the request
    // http.setRequestHeader('Content-type', 'text/plain');

    http.onreadystatechange = function() {//Call a function when the state changes.
        if (http.readyState == 4 && http.status == 200) {
            const smartbin_state = JSON.parse(this.responseText);
            console.log(smartbin_state);
            // console.log("Data received: ");
            // console.log(server_data);
            Data.dailyAverage.organik = smartbin_state.organic;
            Data.dailyAverage.anorganik = smartbin_state.anorganic;
            // alert("Data received!");
            loadDataWeek();
            // update_data();
            // Server.lastUpdate = (new Date()).getTime();;
        }
    }
    http.send();
}

function myFunction() {
    var element = document.body;
    element.classList.toggle("dark-mode");
}

function loadDataWeek() {
    chart_dailyavg.data.datasets[0].data = Data.dailyAverage.organik;
    chart_dailyavg.data.datasets[1].data = Data.dailyAverage.anorganik;
    chart_dailyavg.update();
}

function loadDataSmartBin() {
    var meter_o = document.querySelector('#organik meter');
    var meter_a = document.querySelector('#anorganik meter');

    var value_o = document.querySelector('#organik .value');
    var value_a = document.querySelector('#anorganik .value');

    meter_o.value = Data.current.organik / 100;
    meter_a.value = Data.current.anorganik / 100;

    value_o.innerHTML = Data.current.organik + '%';
    value_a.innerHTML = Data.current.anorganik + '%';
}

function getPrevDay(day) {
    const now  = new Date();
    const date = new Date();
    date.setDate(now.getDate() - day);
    return date;
}

var chart_dailyavg;
var ctx;
var resizeCanvas = () => {};
var init = () => {
    var arrDate = [
        getPrevDay(6),
        getPrevDay(5),
        getPrevDay(4),
        getPrevDay(3),
        getPrevDay(2),
        getPrevDay(1),
        getPrevDay(0)
    ];
    var arrTgl = [
        `${arrDate[0].getDate()}/${arrDate[0].getMonth() + 1}/${arrDate[0].getFullYear()}`,
        `${arrDate[1].getDate()}/${arrDate[1].getMonth() + 1}/${arrDate[1].getFullYear()}`,
        `${arrDate[2].getDate()}/${arrDate[2].getMonth() + 1}/${arrDate[2].getFullYear()}`,
        `${arrDate[3].getDate()}/${arrDate[3].getMonth() + 1}/${arrDate[3].getFullYear()}`,
        `${arrDate[4].getDate()}/${arrDate[4].getMonth() + 1}/${arrDate[4].getFullYear()}`,
        `${arrDate[5].getDate()}/${arrDate[5].getMonth() + 1}/${arrDate[5].getFullYear()}`,
        `${arrDate[6].getDate()}/${arrDate[6].getMonth() + 1}/${arrDate[6].getFullYear()}`
    ];
    ctx = document.getElementById('myChart');
    chart_dailyavg = new Chart(ctx, { 
        type: 'bar',
        data: {
            labels: arrTgl,
            datasets: [
                {
                    label: 'Organik',
                    data: Data.dailyAverage.organik,
                    borderWidth: 0,
                    backgroundColor: '#1c9939',
                    borderRadius: 20
                },
                {
                    label: 'Anorganik',
                    data: Data.dailyAverage.anorganik,
                    borderWidth: 0,
                    backgroundColor: '#afb922',
                    borderRadius: 20
                }
            ]
        },
        
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            responsive:true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    }
                }]
            }
        }
    });
    // setInterval(() => {
    //   ctx.height = 300;
    // }, 0);

    resizeCanvas = () => {
        ctx.style.width = (document.querySelector(".card.block-card").clientWidth - 60) + 'px';
        ctx.style.height = (document.querySelector(".card.block-card").clientHeight - 75) + 'px';
    }
    
    window.addEventListener("resize", resizeCanvas);
    resizeCanvas();

    chart_dailyavg.options.animation = false; // disables all animations
    chart_dailyavg.options.animations.colors = false; // disables animation defined by the collection of 'colors' properties
    chart_dailyavg.options.animations.x = false; // disables animation defined by the 'x' property
    chart_dailyavg.options.transitions.active.animation.duration = 0; // disables the animation for 'active' mode

    openTab('home');
    loadDataSmartBin();
    dataSmartBin();
    loadDataWeek();
    dataWeek();
}