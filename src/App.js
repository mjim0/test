import React, { useState, useEffect } from 'react';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import logo from './logo.svg';
import './App.css';

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
                .bindPopup('Your Current Location')
                .openPopup();
        }
    }, [coordinates]);

    return (
        <div>
            <p style={{ color: 'white' }}>Latitude: {coordinates.latitude}</p>
            <p style={{ color: 'white' }}>Longitude: {coordinates.longitude}</p>
            <div id="map" style={{ width: '50%', height: '400px', margin: '0 auto' }}></div>
        </div>
    );
}

function ToBuidling() {
  const [isOpen, setIsOpen] = useState(false);
  const [selectedOption, setSelectedOption] = useState(null);

  const options = ['Baldy', 'Clemens', 'Lockwood'];

  const toggleDropdown = () => {
    setIsOpen(!isOpen);
  };

  const handleOptionClick = (option) => {
    setSelectedOption(option);
    setIsOpen(false);
  };

  return (
    <div className="dropdown">
      <button onClick={toggleDropdown}>
        {selectedOption ? selectedOption : 'Building'}
      </button>
      {isOpen && (
        <div className="dropdown-menu">
          {options.map((option, index) => (
            <div key={index} onClick={() => handleOptionClick(option)}>
              {option}
            </div>
          ))}
        </div>
      )}
    </div>
  );
}

function ToRoom() {
  const [isOpen, setIsOpen] = useState(false);
  const [selectedOption, setSelectedOption] = useState(null);

  const options = ['101', '102', '103'];

  const toggleDropdown = () => {
    setIsOpen(!isOpen);
  };

  const handleOptionClick = (option) => {
    setSelectedOption(option);
    setIsOpen(false);
  };

  return (
    <div className="dropdown">
      <button onClick={toggleDropdown}>
        {selectedOption ? selectedOption : 'Room'}
      </button>
      {isOpen && (
        <div className="dropdown-menu">
          {options.map((option, index) => (
            <div key={index} onClick={() => handleOptionClick(option)}>
              {option}
            </div>
          ))}
        </div>
      )}
    </div>
  );
}


function FromBuidling() {
  const [isOpen, setIsOpen] = useState(false);
  const [selectedOption, setSelectedOption] = useState(null);

  const options = ['Baldy', 'Clemens', 'Lockwood'];

  const toggleDropdown = () => {
    setIsOpen(!isOpen);
  };

  const handleOptionClick = (option) => {
    setSelectedOption(option);
    setIsOpen(false);
  };

  return (
    <div className="dropdown">
      <button onClick={toggleDropdown}>
        {selectedOption ? selectedOption : 'Building'}
      </button>
      {isOpen && (
        <div className="dropdown-menu">
          {options.map((option, index) => (
            <div key={index} onClick={() => handleOptionClick(option)}>
              {option}
            </div>
          ))}
        </div>
      )}
    </div>
  );
}

function FromRoom() {
  const [isOpen, setIsOpen] = useState(false);
  const [selectedOption, setSelectedOption] = useState(null);

  const options = ['101', '102', '103'];

  const toggleDropdown = () => {
    setIsOpen(!isOpen);
  };

  const handleOptionClick = (option) => {
    setSelectedOption(option);
    setIsOpen(false);
  };

  return (
    <div className="dropdown">
      <button onClick={toggleDropdown}>
        {selectedOption ? selectedOption : 'Room'}
      </button>
      {isOpen && (
        <div className="dropdown-menu">
          {options.map((option, index) => (
            <div key={index} onClick={() => handleOptionClick(option)}>
              {option}
            </div>
          ))}
        </div>
      )}
    </div>
  );
}


function App() {
    const [buildingDropdownVisible, setBuildingDropdownVisible] = useState(false);
    const [roomDropdownVisible, setRoomDropdownVisible] = useState(false);

    const toggleBuildingDropdown = () => {
        setBuildingDropdownVisible(!buildingDropdownVisible);
        setRoomDropdownVisible(false);
    };

    const toggleRoomDropdown = () => {
        setRoomDropdownVisible(!roomDropdownVisible);
        setBuildingDropdownVisible(false);
    };

    return (
        <body>
            <h1>SUNY Buffalo Campus Navigation</h1>
            <div className='center'>

                <div className='button-container center'>
                    <FromBuidling />
                    <FromRoom />
                    <ToBuidling />
                    <ToRoom />
                </div>
            </div>
            <GeolocationMap />
        </body>
    );
}

export default App;