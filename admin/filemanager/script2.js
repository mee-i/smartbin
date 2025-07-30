/*
    script.js
    File Manager system by Nanda
    @2023
*/

var FileManager = {
    dir: "/home/smartbin/",
    dirDisplay: document.getElementById('directory'),
    table: document.getElementById('item-list'),
    loader: document.querySelector('#loader div'),
    moreMenu: document.getElementById('more-menu'),
    dropHint: document.getElementById('drop-hint'),
    moreMenuOpen: false,
    currentSubDir: '',
    currentElem: null,
    isLoading: false,
    isRenaming: false,
    lastCheckDrag: null,
    lastCheckNoDrag: null
}

function updateDir() {
    FileManager.dirDisplay.innerHTML = "/" + FileManager.dir.replace(/\//g, ' &gt; ');
}

function doLoader() {
    FileManager.isLoading = true;
    FileManager.table.classList.add("table-loading");
    FileManager.loader.hidden = false;
}

function stopLoader() {
    FileManager.isLoading = false;
    if (FileManager.table.classList.contains('table-loading'))
        FileManager.table.classList.remove('table-loading');
    FileManager.loader.hidden = true;
}

function postReq(data, onReceive = () => {}) {
    var http = new XMLHttpRequest();
    http.open("POST", "");
    http.onreadystatechange = function() {//Call a function when the state changes.
        if (http.readyState == 4 && http.status == 200) {
            onReceive(this);
        }
    }
    http.send(data);
}

function getReq(link, onReceive = () => {}) {
    var http = new XMLHttpRequest();
    link = "?" + link /*.replace(/\//g, '%2F')*/;
    http.open("GET", link);
    http.onreadystatechange = function() {//Call a function when the state changes.
        if (http.readyState == 4 && http.status == 200) {
            onReceive(this);
        }
    }
    http.send();
    doLoader();
}

function updateList(e) {
    FileManager.table.innerHTML = e.responseText;
    updateDir();
    // FileManager.dirDisplay.innerHTML += FileManager.dir;
    stopLoader();
}

function reqDir(mode = 'open', dir = FileManager.dir) {
    var link = `mode=${mode}&dir=${dir}&tableonly=`;
    getReq(link, updateList);
}

function opendir(dir = '', savestate = true) {
    if (FileManager.isLoading || FileManager.isRenaming) return;
    FileManager.dir += dir;
    var link = `?mode=open&dir=${FileManager.dir}` /*.replace(/\//g, '%2F')*/;
    reqDir();
    if (savestate) history.pushState({dir: FileManager.dir}, "", link);
}

function openroot(savestate = true) {
    if (FileManager.isLoading) return;
    FileManager.dir = "/";
    var link = `?mode=open&dir=${FileManager.dir}`;
    reqDir();
    if (savestate) history.pushState({dir: FileManager.dir}, "", link);
}

function openfile(dir) {
    if (FileManager.isLoading || FileManager.isRenaming) return;
    // FileManager.dir += dir;
    location.href = "?mode=open&dir=" + FileManager.dir + dir;
}

function updir(savestate = true) {
    if (FileManager.isLoading) return;
    FileManager.dir = FileManager.dir.replace(/\/[^\/]*.$/, '/');
    var link = `?mode=open&dir=${FileManager.dir}`;
    reqDir();
    if (savestate) history.pushState({dir: FileManager.dir}, "", link);
}

function deletedir(dir = FileManager.dir + FileManager.currentSubDir) {
    if (FileManager.isLoading) return;
    reqDir('delete', dir);
}

function submitname() {
    if (FileManager.isLoading || !FileManager.isRenaming) return;
    var dir = FileManager.dir + FileManager.currentSubDir;
    var dirElem = FileManager.currentElem.querySelector('.dirname');
    var newName = '';
    dirElem.contentEditable = false;
    newName = dirElem.innerText;
    
    if (newName == '') {
        dirElem.innerText = FileManager.currentElem.getAttribute('dir').replace(/\//g, '');
        FileManager.isRenaming = false;
        return;
    }

    var link = `mode=rename&dir=${dir}&newname=${newName}`;
    getReq(link, e => {
        updateList(e);
        FileManager.isRenaming = false;
    });
}

function renamedir() {
    if (FileManager.isLoading || FileManager.isRenaming) return;
    var dirElem = FileManager.currentElem.querySelector('.dirname');
    dirElem.contentEditable = true;
    FileManager.isRenaming = true;
    dirElem.onkeydown = e => {
        if (!FileManager.isRenaming) return;
        if (e.key == 'Enter') submitname();
    };
    dirElem.onblur = submitname;
    dirElem.focus();
}

function newFolder() {
    // reqDir('mkdir', FileManager.dir);
    var link = `mode=mkdir&dir=${FileManager.dir}`;
    getReq(link, e => {
        updateList(e);
        FileManager.currentElem = document.querySelector('[newdir]');
        FileManager.currentSubDir = FileManager.currentElem.getAttribute('dir');
        renamedir();
    });
}

function dirclick(e) {
    opendir(e.parentNode.getAttribute('dir'));
}

function fileclick(e) {
    openfile(e.parentNode.getAttribute('dir'));
}

window.onpopstate = (event) => {
    if (event.state.dir)
        FileManager.dir = event.state.dir;
    else {
        FileManager.dir = "/";
    }
    opendir('', false);
}

// function uploadFile(file) {
//     let formData = new FormData();
  
//     formData.append('file', file);
  
//     fetch(`?tableonly=&dir=${FileManager.dir}&mode=upload`, {
//         method: 'POST',
//         body: formData
//     })
//     .then(() => { e => FileManager.table.innerHTML = e.text() })
//     .catch(() => { /* Error. Inform the user */ });
// } 

function uploadFile(file) {
    doLoader();
    var formdata = new FormData();
    formdata.append("file", file);
    formdata.append("dir", FileManager.dir);
    var ajax = new XMLHttpRequest();
    // ajax.upload.addEventListener("progress", updateProgress, false);
    // ajax.addEventListener("load", progressComplete, false);
    ajax.open("POST", `?tableonly=&dir=${FileManager.dir}&mode=upload`);
    ajax.onreadystatechange = function() {//Call a function when the state changes.
        if (ajax.readyState == 4 && ajax.status == 200) {
            updateList(this);
        }
    }
    ajax.send(formdata);
}

function openmore(e) {
    var elemPos = e.getBoundingClientRect();
    FileManager.currentElem = e.parentNode;
    FileManager.moreMenuOpen = false;
    FileManager.moreMenu.style.display = 'none';
    FileManager.currentSubDir = e.parentNode.getAttribute('dir');
    FileManager.moreMenu.style.right = (window.innerWidth - elemPos.right + scrollX) + "px";
    FileManager.moreMenu.style.top = (elemPos.top + scrollY) + "px";
    FileManager.moreMenu.style.display = 'inline-block';
    console.log(elemPos);
    setTimeout(() => FileManager.moreMenuOpen = true, 100);
}

function closemore() {
    if (!FileManager.moreMenuOpen) return;
    FileManager.moreMenu.style.display = 'none';
    FileManager.moreMenuOpen = false;
}

function closeActions(e) {
    closemore();
    // submitname();
}

window.onclick = closeActions;

document.getElementById("uploadfile").addEventListener("change", function(event) {
    var files = [...event.target.files];
    files.forEach(uploadFile);
});

onscroll = () => {
    closemore();
};

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    addEventListener(eventName, preventDefaults, false);
});
  
function preventDefaults (e) {
    e.preventDefault();
    e.stopPropagation();
}

addEventListener('drop', e => {
    e.preventDefault();
    e.stopPropagation();
    console.log(e);
    console.log(e.dataTransfer.files);
    ([...e.dataTransfer.files]).forEach(uploadFile);
}, false);

function showDropHint() {
    if (!FileManager.dropHint.classList.contains('show-drop-hint'))
        FileManager.dropHint.classList.add('show-drop-hint');
}

function hideDropHint() {
    FileManager.dropHint.classList.remove('show-drop-hint');
}

function checkDrag() {
    clearTimeout(FileManager.lastCheckNoDrag);
    FileManager.lastCheckDrag = setTimeout(showDropHint);
}

function checkNoDrag() {
    FileManager.lastCheckNoDrag = setTimeout(hideDropHint, 300);
}

document.body.addEventListener('dragleave', checkNoDrag, false);
document.body.addEventListener('drop', checkNoDrag, false);
document.body.addEventListener('dragenter', checkDrag, false);
document.body.addEventListener('dragover', checkDrag, false);

history.replaceState({ dir: FileManager.dir }, '', '');