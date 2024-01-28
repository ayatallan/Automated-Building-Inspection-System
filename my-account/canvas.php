

<div align="middle" style="width:100%;" >

    <button class="btn btn-b" style="float:cetner;margin-right:22px;" onclick="saveCanvas();"> Save Image </button>
    
    <label class="btn btn-b" style="margin-right:22px;"> Change Background<input type="file" id="imageInput" onchange="change_background(this);" style="float:center; margin-right:22px;" hidden></label>
    <button class="btn btn-b" style="float:center;" onclick="resetBackground();">Reset Background</button>



      
    <style>
        h1 {
            color: green;
        }

        #test_canvas {
            float:center;
            margin:44px;
        }
    </style>
    
    <script>
      var scale_meter_to_pixel=35.37;//each meter convert to pixel
      
      function data_getted_by_mqtt_to_canvas(topic_name,message_arrived)
      {
	      
	      if(topic_name=="project_topic/automated_building_inspection_system/location")
	      {
	      
	    
		      
		      var valuesArray = message_arrived.split(',');
		      var x_meter=valuesArray[0];
		      var y_meter=valuesArray[1];
		      var type=valuesArray[2];
		      
		      
		      var x_pixel=x_meter*scale_meter_to_pixel;
		      var y_pixel=y_meter*scale_meter_to_pixel;
		      
		      
          //============================================= 
          //new origin 162 from left and 277 from top and if y increase move up not down
          //image on gimp ,ust be same size of canvas 512X480

          x_pixel=parseInt(x_pixel)+162;// this 162 --> distance from left to new origin
          y_pixel=277-parseInt(y_pixel)// this 277  --> distance from top to new origin
          
          /*
          ##(analysis)##
          x_pixel=parseInt(x_pixel)+162;//162 --> distance from left to new origin
		  y_pixel=(canvas.height-parseInt(y_pixel))-(canvas.height-277);//277  --> distance from top to new origin
		      */     
          //=============================================
          
          
		      var color="#555";
		      
		      if(type=="1 structural defect")
		      {
		        color="#fad02c";
		      }
		      else if(type=="0 cracks")
		      {
		        color="#5593CE";
		      }
		      
          draw(x_pixel, y_pixel,color,x_meter,y_meter);

	      }

      }
		      
      </script>



    <center>

        <canvas id="test_canvas"> </canvas>

        <table style="border:1px solid #aaaa;position:absolute;left:22px;top:111px;font-weight:bold;background:#f7f7f7;padding:11px;">
          <tr>
            <td>Structural defect</td>
            <td ><div style="background-color:#fad02c;width:22px;height:22px;border-radius:33px;margin:11px;"></div></td>
          </tr>
          <tr>
            <td>Cracks</td>
            <td ><div style="background-color:#5593CE;width:22px;height:22px;border-radius:33px;margin:11px;"></div></td>
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
   
            // This function will do the animation
            function draw(x, y,color,x_meter,y_meter) {
                // Creating a circle
                
                l.beginPath();
                l.strokeStyle = color;
                l.fillStyle = color; 
                l.arc(x, y, radius, 0, Math.PI * 2, true);
                l.fill();
                l.stroke();
                
                // Adding a label with background
                const label = '(' + x_meter.toString() + ',' + y_meter.toString() + ')';
                const labelX = x;
                const labelY = y + radius + 15;

                // Set background color
                l.fillStyle = '#fff'; // Adjust the alpha value for transparency
                const textWidth = l.measureText(label).width;
                l.fillRect(labelX - textWidth / 2, labelY - 12, textWidth, 12); // Adjust height based on font size

                // Set text properties
                l.fillStyle = 'black'; // Set the color of the label text
                l.font = '12px Arial'; // Set the font style and size
                l.textAlign = 'center'; // Align the text to the center horizontally

                // Draw the text label
                l.fillText(label, labelX, labelY);
                
    
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

            function change_background(input) {
                var file = input.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        var newImage = new Image();
                        newImage.src = e.target.result;

                        newImage.onload = function () {
                            // Update the canvas background with the new image
                            l.clearRect(0, 0, canvas.width, canvas.height);
                            l.drawImage(newImage, 0, 0, canvas.width, canvas.height);

                            // Optionally, save the new image as the current background
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
            }
            
        
        </script>
    </center>


