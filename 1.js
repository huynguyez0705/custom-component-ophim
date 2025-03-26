const customButton = document.querySelector('#page-info a.custom-button')

customButton ? (document.querySelector('#page-info .poster .adspruce-streamlink') ? (document.querySelector('#page-info .poster .adspruce-streamlink').style.maxHeight = '350px') : null) : null
function markPopupAsOpened(deviceType, index) {
    localStorage.setItem("popupOpenedTime", Date.now());
    localStorage.setItem(popupIndex_$ư{deviceType}, index);
}