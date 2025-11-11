function  updateClock(){
    const clock = document.getElementById('clock');
    const now = new Date();

    let hours = now.getHours();
    let minutes = now.getMinutes();
    let seconds = now.getSeconds();

    //Format time(e.g 09:05:01)
   hours = hours < 10 ? "0" + hours : hours;
   minutes = minutes < 10 ? "0" + minutes : minutes;
  seconds = seconds < 10 ? "0" + seconds : seconds;
  
  clock.innerHTML = `${hours}:${minutes}:${seconds}`;

}

//Update every second
setInterval(updateClock,1000);
updateClock();