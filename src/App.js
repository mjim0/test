import React, { useState, useEffect } from 'react';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

function GeolocationMap() {
    const [coordinates, setCoordinates] = useState({ latitude: null, longitude: null });

    useEffect(() => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                position => {
                    setCoordinates({
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    });
                },
                error => {
                    console.error('Error getting geolocation:', error.message);
                }
            );
        } else {
            console.error('Geolocation is not supported by this browser.');
        }
    }, []);

    useEffect(() => {
        if (coordinates.latitude !== null && coordinates.longitude !== null) {
            const map = L.map('map').setView([coordinates.latitude, coordinates.longitude], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            L.marker([coordinates.latitude, coordinates.longitude]).addTo(map)
                .bindPopup('Your Location')
                .openPopup();
        }
    }, [coordinates]);

    return (
        <div>
            <h1>Your Coordinates</h1>
            <p>Latitude: {coordinates.latitude}</p>
            <p>Longitude: {coordinates.longitude}</p>
            <div id="map" style={{ width: '80%', height: '400px', margin: '0 auto' }}></div>
        </div>
    );
}

export default GeolocationMap;