const apiKey = "6f86ef633d345e0ca32e275e3ce0fdf4"; // Replace with your OpenWeatherMap API key

const getWeatherBtn = document.getElementById("getWeather");
const cityInput = document.getElementById("city");
const weatherInfo = document.getElementById("weatherInfo");
const errorMessage = document.getElementById("error");

getWeatherBtn.addEventListener("click", async () => {
  const city = cityInput.value.trim();

  // Clear previous data
  weatherInfo.innerHTML = "";
  errorMessage.textContent = "";

  if (!city) {
    errorMessage.textContent = "⚠️ Please enter a city name.";
    return;
  }

  try {
    // Fetch weather data from API
    const response = await fetch(
      `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric`
    );

    if (!response.ok) {
      throw new Error("City not found");
    }

    const data = await response.json();

    // Extract useful data
    const { name, main, weather } = data;
    const temperature = Math.round(main.temp);
    const description = weather[0].description;
    const icon = weather[0].icon;

    // Display weather info
    weatherInfo.innerHTML = `
      <h3>${name}</h3>
      <img src="https://openweathermap.org/img/wn/${icon}@2x.png" alt="${description}" />
      <p><strong>${temperature}°C</strong></p>
      <p>${description.charAt(0).toUpperCase() + description.slice(1)}</p>
    `;
  } catch (error) {
    errorMessage.textContent = "❌ City not found or network error.";
    console.error(error);
  }
});
