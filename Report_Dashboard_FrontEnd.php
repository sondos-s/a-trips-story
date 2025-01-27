<?php
    include 'Report_Dashboard_BackEnd.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="ViewStyles/ViewStyles.css">
    <link rel="stylesheet" href="ViewStyles/OwnerViewStyles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=IM Fell French Canon SC' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=IM Fell English' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Jost' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Report</title>
</head>

<body>
    <?php include 'Header_Owner.php' ?>

    <h3 class="dashboard-header" style="font-size: 18px; margin-bottom: 400px;">Reports in Dashboard</h3>
    <div id="pdf-content" class="chart-grid">
        <div class="chart-container">
            <h5>Booked/Non Chart</h5>
            <canvas id="tripDataChart"></canvas>
        </div>
        <div class="chart-container">
            <h5>Participants By Cities Chart</h5>
            <canvas id="participantsChart"></canvas>
        </div>
        <div class="chart-container">
            <h5>Registered Users Participation Chart</h5>
            <canvas id="participationChart"></canvas>
        </div>
        <div class="chart-container">
            <h5>Top 5 Rated Trips</h5>
            <h6>Average Ratings<h6>
            <canvas id="averageRatingsChart"></canvas>
        </div>
    </div>

</body>
</html>

<script>
    // Get the canvas element
    var ctx = document.getElementById('participantsChart').getContext('2d');

    // Assuming you have fetched the data and stored it in $cities and $participantsCount
    var cities = <?php echo json_encode($cities); ?>;
    var participantsCount = <?php echo json_encode($participantsCount); ?>;

    // Create the doughnut chart
    var participantsChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: cities, // Use city names as labels
            datasets: [{
                data: participantsCount,
                backgroundColor: [
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(54, 162, 235)',
                'rgb(153, 102, 255)',
                'rgb(201, 203, 207)',
                'rgb(140, 140, 140)',
            ],
            }]
        },
        options: {
            responsive: false, // Disable aspect ratio maintenance
            maintainAspectRatio: false, // Disable aspect ratio maintenance
            legend: {
                position: 'top',
            },
            plugins: {
                legend: {
                    labels: {
                        fontSize: 10, // Adjust the font size of legend labels
                    },
                },
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        var cityIndex = tooltipItem.index;
                        var cityName = data.labels[cityIndex];
                        var participantCount = data.datasets[0].data[cityIndex];
                        return cityName + ': ' + participantCount;
                    }
                }
            }
        }
    });
</script>


<script>
    var ctx = document.getElementById('participationChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Booked Users', 'Non-Booked Users'],
                    datasets: [{
                        data: [<?php echo $bookedUsers; ?>, <?php echo $nonBookedUsers; ?>],
                        backgroundColor: ['rgba(75, 192, 192, 0.7)','rgba(54, 162, 235, 0.7)'],
                    }]
                },
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: 'User Participation Chart'
                    }
                }
            });
</script>

<script>
    // Assuming you have fetched the data and stored it in the arrays
    var tripTitles = <?php echo json_encode($tripTitles); ?>;
    var tripParticipantsCount = <?php echo json_encode($tripParticipantsCount); ?>;
    var tripNonBookedPlaces = <?php echo json_encode($tripNonBookedPlaces); ?>;
    var tripTotalPriceSum = <?php echo json_encode($tripTotalPriceSum); ?>;

    // Create the bar chart
    var ctx = document.getElementById('tripDataChart').getContext('2d');
    var tripDataChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: tripTitles,
            datasets: [
                {
                    label: 'Participants',
                    data: tripParticipantsCount,
                    backgroundColor: 'rgb(255, 99, 132)'
                },
                {
                    label: 'Non-Booked Places',
                    data: tripNonBookedPlaces,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)'
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });

</script>

<script>
    // Get the canvas element
    var ctx = document.getElementById('averageRatingsChart').getContext('2d');

    // Assuming you have fetched the data and stored it in $tripTitles and $averageRatings
    var tripTitles = <?php echo json_encode($tripTitles); ?>;
    var averageRatings = <?php echo json_encode($averageRatings); ?>;

    // Create the bar chart for average ratings
    var averageRatingsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: tripTitles,
            datasets: [
                {
                    label: 'Average Rating',
                    data: averageRatings,
                    backgroundColor: 'rgb(75, 192, 192)'
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    beginAtZero: true,
                    max: 5 // Assuming ratings are on a scale of 0 to 5
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>