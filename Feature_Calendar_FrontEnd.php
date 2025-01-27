<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="ViewStyles/HomeViewStyles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=IM Fell French Canon SC' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=IM Fell English' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Jost' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
    <title>Calendar</title>
</head>
<body>
    <div class="calendar-container">
        <div class="calendar-header">
            <button id="prevBtn">&lt;</button>
            <h2 id="monthYear"></h2>
            <button id="nextBtn">&gt;</button>
        </div>
        <div id="calendar"> 
        </div>
    </div>
</body>
</html>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const monthYear = document.getElementById("monthYear");
    const calendar = document.getElementById("calendar");
    const prevBtn = document.getElementById("prevBtn");
    const nextBtn = document.getElementById("nextBtn");

    const daysOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();

    async function fetchTrips(month, year) {
        const response = await fetch(`Feature_Calendar_BackEnd.php?month=${month}&year=${year}`);
        const data = await response.json();
        return data;
    }

    function updateCalendar() {
        monthYear.textContent = new Date(currentYear, currentMonth).toLocaleString("default", {
            month: "long",
            year: "numeric"
        });

        const firstDayOfMonth = new Date(currentYear, currentMonth, 1).getDay();
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();

        calendar.innerHTML = "";

        for (const dayOfWeek of daysOfWeek) {
            const dayHeader = document.createElement("div");
            dayHeader.classList.add("day", "day-header");
            dayHeader.textContent = dayOfWeek;
            calendar.appendChild(dayHeader);
        }

        for (let i = 0; i < firstDayOfMonth; i++) {
            const emptyDay = document.createElement("div");
            emptyDay.classList.add("day", "empty");
            calendar.appendChild(emptyDay);
        }

        for (let i = 1; i <= daysInMonth; i++) {
            const day = document.createElement("div");
            day.classList.add("day");
            day.textContext = i;

            const dayNumber = document.createElement("div");
            dayNumber.classList.add("day-number");
            dayNumber.textContent = i;

            day.appendChild(dayNumber);
        }


         fetchTrips(currentMonth + 1, currentYear)
            .then(trips => {
                calendar.innerHTML = ""; // Clear the calendar before updating

                for (const dayOfWeek of daysOfWeek) {
                    const dayHeader = document.createElement("div");
                    dayHeader.classList.add("day", "day-header");
                    dayHeader.textContent = dayOfWeek;
                    calendar.appendChild(dayHeader);
                }

                for (let i = 0; i < firstDayOfMonth; i++) {
                    const emptyDay = document.createElement("div");
                    emptyDay.classList.add("day", "empty");
                    calendar.appendChild(emptyDay);
                }

                for (let i = 1; i <= daysInMonth; i++) {
                    const day = document.createElement("div");
                    day.classList.add("day");
                    day.textContent = i;

                    const tripInfo = trips[i+1];

                    if (tripInfo) {
                        const tripTitle = document.createElement("a");
                        tripTitle.classList.add("tripTitle");
                        tripTitle.textContent = tripInfo.title;

                        const tripURL = `TripDetails_FrontEnd.php?trip_id=${tripInfo.tripId}&tripTitle=${encodeURIComponent(tripInfo.title)}`;
                        tripTitle.href = tripURL;

                        // Wrapper div for the trip title to set its background color
                        const tripTitleWrapper = document.createElement("div");
                        tripTitleWrapper.classList.add("trip-title-wrapper");
                        tripTitleWrapper.style.backgroundColor = "#fffd8d";
                        tripTitleWrapper.appendChild(tripTitle);

                        day.appendChild(tripTitleWrapper);
                    }

                    calendar.appendChild(day);
                    
                    // Check if the current day is "today" and add the pin emoji
                    if (i === currentDate.getDate() && currentMonth === currentDate.getMonth() && currentYear === currentDate.getFullYear()) {
                        const pinEmoji = document.createElement("span");
                        pinEmoji.textContent = "📌"; // Pin emoji
                        pinEmoji.classList.add("today-pin"); // Add a class for styling
                        day.appendChild(pinEmoji);
                    }
                }
            });
        }

    updateCalendar();

    prevBtn.addEventListener("click", () => {
        if (currentMonth > 0) {
            currentMonth--;
        } else {
            currentMonth = 11;
            currentYear--;
        }
        updateCalendar();
    });

    nextBtn.addEventListener("click", () => {
        if (currentMonth < 11) {
            currentMonth++;
        } else {
            currentMonth = 0;
            currentYear++;
        }
        updateCalendar();
    });
    
});


</script>
