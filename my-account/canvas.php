<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canvas Drawing</title>
    <style>
        h1 {
            color: green;
        }

        #test_canvas {
            float: center;
            margin: 44px;
        }
    </style>
</head>

<body>

    <div align="middle" style="width:100%;">

        <button class="btn btn-b" style="float:center;margin-right:22px;" onclick="saveCanvas();"> Save Image </button>

        <label class="btn btn-b" style="margin-right:22px;">
            Change Background<input type="file" id="imageInput" onchange="change_background(this);" style="float:center; margin-right:22px;" hidden>
        </label>
        <button class="btn btn-b" style="float:center;" onclick="resetBackground();">Reset Background</button>
        <button class="btn btn-b" style="float:center;" onclick="generatePDF();">Generate PDF</button>

        <style>
            h1 {
                color: green;
            }

            #test_canvas {
                float: center;
                margin: 44px;
            }
        </style>
        <script src="https://rawgit.com/eKoopmans/html2pdf/master/dist/html2pdf.bundle.js"></script>

        <script>
            var scale_meter_to_pixel = 35.37; // each meter converts to pixel
            var circles = [];

            function data_getted_by_mqtt_to_canvas(topic_name, message_arrived) {

                if (topic_name == "project_topic/automated_building_inspection_system/location") {

                    var valuesArray = message_arrived.split(',');
                    var x_meter = valuesArray[0];
                    var y_meter = valuesArray[1];
                    var type = valuesArray[2];

                    console.log(`Received point before scaling: (${x_meter}, ${y_meter}, ${type})`);

                    var x_pixel = x_meter * scale_meter_to_pixel;
                    var y_pixel = y_meter * scale_meter_to_pixel;

                    x_pixel = parseInt(x_pixel) + 162;
                    y_pixel = 277 - parseInt(y_pixel);

                    console.log(`Received point after scaling: (${x_pixel}, ${y_pixel}, ${type})`);

                    var color = "#555";

                    if (type == "structural_defect") {
                        color = "#fad02c";
                    } else if (type == "cracks") {
                        color = "#5593CE";
                    }

                    draw(x_pixel, y_pixel, color, x_meter, y_meter, type);

                }
            }

            function draw(x, y, color, x_meter, y_meter, type) {
                if (checkMinDistance(x, y, type)) {
                    var canvas = document.getElementById("test_canvas");
                    var l = canvas.getContext('2d');
                    var radius = 5;

                    l.beginPath();
                    l.strokeStyle = color;
                    l.fillStyle = color;
                    l.arc(x, y, radius, 0, Math.PI * 2, true);
                    l.fill();
                    l.stroke();

                    const label = '(' + x_meter.toString() + ',' + y_meter.toString() + ')';
                    const labelX = x;
                    const labelY = y + radius + 15;

                    l.fillStyle = '#fff';
                    const textWidth = l.measureText(label).width;
                    l.fillRect(labelX - textWidth / 2, labelY - 12, textWidth, 12);

                    l.fillStyle = 'black';
                    l.font = '12px Arial';
                    l.textAlign = 'center';
                    l.fillText(label, labelX, labelY);
                }
            }

            function checkMinDistance(x, y, type) {
                const minDistance = 20;

                for (const circle of circles) {
                    const distance = Math.sqrt((x - circle.x) ** 2 + (y - circle.y) ** 2);
                    if (distance < minDistance && circle.type === type) {
                        console.log(`Ignored point (${x.toFixed(2)}, ${y.toFixed(2)}, ${type}) - Too close to (${circle.x.toFixed(2)}, ${circle.y.toFixed(2)}, ${circle.type})`);
                        return false;
                    }
                }

                circles.push({ x, y, type });
                return true;
            }
        </script>

        <center>

            <canvas id="test_canvas"></canvas>

            <table
                style="border:1px solid #aaaa;position:absolute;left:22px;top:111px;font-weight:bold;background:#f7f7f7;padding:11px;">
                <tr>
                    <td>Structural defect</td>
                    <td>
                        <div style="background-color:#fad02c;width:22px;height:22px;border-radius:33px;margin:11px;">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Cracks</td>
                    <td>
                        <div style="background-color:#5593CE;width:22px;height:22px;border-radius:33px;margin:11px;">
                        </div>
                    </td>
                </tr>
            </table>
            <script>
                var canvas = document.getElementById("test_canvas");

                canvas.width = 512;
                canvas.height = 480;

                var l = canvas.getContext('2d');
                var backgroundImage = new Image();
                backgroundImage.src = '../assets/images/canvas_bg.png'; // Specify the path to your background image
                l.drawImage(backgroundImage, 0, 0, canvas.width, canvas.height);

                var radius = 5;

                var originalBackgroundImage = new Image();
                originalBackgroundImage.src = backgroundImage.src;

                function clear() {
                    l.clearRect(0, 0, innerWidth, innerHeight);
                }

                function saveCanvas() {
                    var link = document.createElement('a');
                    link.href = canvas.toDataURL();
                    link.download = 'canvas_image.png';
                    link.click();
                }

                function change_background(input) {
                    var file = input.files[0];
                    if (file) {
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            var newImage = new Image();
                            newImage.src = e.target.result;

                            newImage.onload = function () {
                                l.clearRect(0, 0, canvas.width, canvas.height);
                                l.drawImage(newImage, 0, 0, canvas.width, canvas.height);

                                backgroundImage.src = e.target.result;
                            };
                        };
                        reader.readAsDataURL(file);
                    }
                }

                function resetBackground() {
                    backgroundImage.src = originalBackgroundImage.src;
                    l.clearRect(0, 0, canvas.width, canvas.height);
                    l.drawImage(backgroundImage, 0, 0, canvas.width, canvas.height);
                    circles = []; // Reset circles array when resetting background
                }

                function generatePDF() {
                    // Get the current date and time
                    var currentDate = new Date();
                    var formattedDate = currentDate.toLocaleDateString();
                    var formattedTime = currentDate.toLocaleTimeString();

                    // Get the first and last name from PHP (assuming you are in a PHP context)
                    var fullName = "<?php echo $row['fname'] . ' ' . $row['lname']; ?>";

                    // Create a div to hold the printable content
                    var pdfElement = document.createElement('div');
                    pdfElement.innerHTML = `
                        <div style="text-align: center; margin-bottom: 20px;">
                            <img src='../assets/images/logo.png' alt="Logo" style="width: 80px; height: 80px; margin-bottom: 10px;">
                            <h1 style="color: #5593CE; font-size: 24px;">Automated Building Inspection Report</h1>
                            <p style="color: #777; font-size: 16px;">${formattedDate} ${formattedTime}</p>
                        </div>
                        <p style="color: #fad02c; font-size: 18px; text-align: center;">Welcome, ${fullName}!</p>
                        <p style="color: #555; font-size: 16px; text-align: center;">
                            This report provides details about the current status of your building and highlights the locations of identified defects.
                        </p>
                        <table style="border-collapse: collapse; width: 70%; margin: auto; text-align: center; margin-top: 20px; background-color: #f7f7f7;">
                            <tr>
                                <th style="border: none; padding: 8px; background-color: #5593CE; color: white;">Defect Type</th>
                                <th style="border: none; padding: 8px; background-color: #5593CE; color: white;">Color</th>
                            </tr>
                            <tr>
                                <td style="border: none; padding: 8px; color:black; ">Structural defect</td>
                                <td style="border: none; padding: 8px; background-color: #f7f7f7; color: #fad02c; font-size:20px;">&bull;</td>
                            </tr>
                            <tr>
                                <td style="border: none; padding: 8px; color:black;">Cracks</td>
                                <td style="border: none; padding: 8px; background-color: #f7f7f7; color: #5593CE; font-size:20px;">&bull;</td>
                            </tr>
                        </table>
                        <p style="color: #555; font-size: 16px; text-align: center;">This map will show you the location of the defects.</p>

                        <canvas id="pdf_canvas" style="display: block; margin: auto; margin-top: 20px; background-color: #f7f7f7;"></canvas>
                        <p style="color: #555; font-size: 16px; text-align: center; margin-top: 20px;">This report is generated by Turtlebot 2 with ID: 123456789.</p>
                    `;

                    // Set up the canvas for drawing
                    var pdfCanvas = pdfElement.querySelector('#pdf_canvas');
                    pdfCanvas.width = 512;
                    pdfCanvas.height = 480;

                    var pdfContext = pdfCanvas.getContext('2d');
                    pdfContext.drawImage(canvas, 0, 0, pdfCanvas.width, pdfCanvas.height);

                    // Generate the PDF using html2pdf
                    html2pdf(pdfElement, {
                        margin: 10,
                        filename: 'building_inspection_report.pdf',
                        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
                    });
                }
            </script>
        </center>
    </div>

</body>

</html>
