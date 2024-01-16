<!DOCTYPE HTML>
<html>

<head>
    <title>
        Bouncing Ball!!
    </title>
    <style>
        h1 {
            color: green;
        }

        canvas {
            width: 600px;
            height: 400px;
            position: absolute;
            top: 20%;
            left: 20%;
        }
    </style>

</head>

<body>
    <center>

        <canvas id="test_canvas"> </canvas>
        <button onclick="saveCanvas()">Save Canvas</button>

        <script>
            var canvas = document.getElementById("test_canvas");
    
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;

            var l = canvas.getContext('2d');
            var backgroundImage = new Image();
            backgroundImage.src = 'assets/images/canvas_bg.png'; // Specify the path to your background image
            l.drawImage(backgroundImage, 0, 0, canvas.width, canvas.height);

            var radius = 20;

            draw(50, 100);
            draw(100, 100);

            // This function will do the animation
            function draw(x, y) {
                // Creating a circle
                l.beginPath();
                l.strokeStyle = "black";
                l.arc(x, y, radius, 0, Math.PI * 2, true);
                l.fill();
                l.stroke();
            }

            function clear() {
                l.clearRect(0, 0, innerWidth, innerHeight); //------>for clear
            }

            function saveCanvas() {

                // Create a link and trigger a download
                var link = document.createElement('a');
                link.href = canvas.toDataURL();
                link.download = 'canvas_image.png';
                link.click();
            }

        </script>
    </center>
</body>

</html>

