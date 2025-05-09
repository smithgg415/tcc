async function getWeather() {
    const cityName = "Presidente Venceslau";
    const API_KEY = '848082604d168d1154ccdd2326eb057e'; 
    const apiURL = `https://api.openweathermap.org/data/2.5/forecast?q=${encodeURI(cityName)}&appid=${API_KEY}&units=metric&lang=pt_br`;

    try {
        const response = await fetch(apiURL);
        const data = await response.json();

        const currentWeather = data.list[0];

        document.getElementById('current-temp').innerText = `${Math.round(currentWeather.main.temp)}°C`;
        document.getElementById('current-desc').innerText = currentWeather.weather[0].description;
        updateWeatherIcon('weather-icon', currentWeather.weather[0].icon);

    } catch (error) {
        console.error('Erro ao obter a previsão do tempo:', error);
        alert('Erro ao buscar a previsão do tempo. Tente novamente mais tarde.');
    }
}

function updateWeatherIcon(elementId, iconCode) {
    const iconElement = document.getElementById(elementId);
    iconElement.innerHTML = `<img src="http://openweathermap.org/img/wn/${iconCode}@2x.png" alt="Weather Icon" width="24">`;
}

getWeather();

setInterval(() => {
    getWeather();
}, 3600000);
