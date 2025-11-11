const  display = document.getElementById("display");
const  buttons = document.querySelectorAll("button");

let currentInput = "";

buttons.forEach(button => {
    button.addEventListener("click",() =>{

   
    const value = button.textContent;

    if(value === "="){
        try{
            currentInput = eval(currentInput); // Evaluate math expression
            display.value = currentInput;

        } catch {
            display.value = "Error";
            currentInput ="";
        }
    } else {
        currentInput +=value;
        display.value = currentInput;
    }
     });
});