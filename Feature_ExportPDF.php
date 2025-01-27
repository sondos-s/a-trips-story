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
    <title></title>
    <style>

    </style>
</head>

<body>
    <?php include 'Header_Owner.php' ?>
    <div class="compactSidebarPDF">
            <button id="export-pdf-button" onmouseover="playSound()"><i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;Export</button>
    </div>
    <audio id="soundPop">
        <source src="ViewStyles/Sounds Effect Audio/popsound.mp3" type="audio/mpeg">
    </audio>
</body>

</html>
<script>
  // Function to export the content as a PDF
  function exportAsPDF() {
    const content = document.getElementById("pdf-content");
    const pdfOptions = {
      margin: 10, // Adjust margin as needed
      filename: "exported-document.pdf", // PDF file name
      image: { type: "jpeg", quality: 0.98 }, // Image format and quality
      html2canvas: { scale: 2 }, // Scale for better resolution
      jsPDF: { unit: "mm", format: "a4", orientation: "portrait" }, // PDF format and orientation
    };

    html2pdf().from(content).set(pdfOptions).save();
  }

  // Attach the export function to the button click event
  const exportButton = document.getElementById("export-pdf-button");
  exportButton.addEventListener("click", exportAsPDF);
</script>

<script>
    // Function to play the sound
    function playSound() {
        var sound = document.getElementById("soundPop");
        if (sound) {
            sound.currentTime = 0; // Reset the sound to the beginning
            sound.play();
        } else {
            console.log("Sound element not found.");
        }
    }
  </script>