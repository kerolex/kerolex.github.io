<?php
session_start();

if (!isset($_SESSION['id'])) {
  header("Location: private.html");
}
?>
<html lang = "en">  
   <head>
      <title>Alessio Xompero</title>
        
      <link href = "css/bootstrap.min.css" rel = "stylesheet">
    
      <style>
         body {
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #ADABAB;
         }
         
         .form-signin {
            max-width: 330px;
            padding: 15px;
            margin: 0 auto;
            color: #017572;
         }
         
         .form-signin .form-signin-heading,
         .form-signin .checkbox {
            margin-bottom: 10px;
         }
         
         .form-signin .checkbox {
            font-weight: normal;
         }
         
         .form-signin .form-control {
            position: relative;
            height: auto;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            padding: 10px;
            font-size: 16px;
         }
         
         .form-signin .form-control:focus {
            z-index: 2;
         }
         
         .form-signin input[type="email"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
            border-color:#017572;
         }
         
         .form-signin input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
            border-color:#017572;
         }
         
         h2{
            text-align: center;
            color: #017572;
         }
      </style>
      
   </head>
   
         <style>
      body {
        background-color: #EBE8E4;
        color: #222;
        font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
        font-weight: 300;
        font-size: 15px;
    }
    </style>

   <body>
     <h1>Alessio Xompero</h1>

     
     <nav style="text-align:center">
        <ul>
        <li><a href="index.html">Home</a></li>
        <li><a href="about.html">About</a></li>
        <li><a href="publications.html">Publications</a></li>
        <li><a href="restricted.php">Private</a></li>
        </ul>
      </nav>
      <div >
                      <form class = "form-signin" role = "form" 
              action = "logout.php"
              method = "POST">
          <button class = "btn btn-lg btn-primary btn-block" type = "submit" 
                 name = "logout">Logout</button>
       </form>
       </div>
       
      
      <style>
        nav {
          background-color: #fff;
          border: 1px solid #dedede;
          border-radius: 4px;
          box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.055);
          color: #888;
          display: block;
          margin: 8px 22px 8px 22px;
          overflow: hidden;
          width: 90%;
        }

        nav ul {
          margin: 0;
          padding: 0;
        }

        nav ul li {
          display: inline-block;
          list-style-type: none;

          -webkit-transition: all 0.2s;
          -moz-transition: all 0.2s;
          -ms-transition: all 0.2s;
          -o-transition: all 0.2s;
          transition: all 0.2s;
        }

        nav > ul > li > a {
          color: #aaa;
          display: block;
          line-height: 56px;
          padding: 0 24px;
          text-decoration: none;
        }
        
        nav > ul > li > a > .caret {
    border-top: 4px solid #aaa;
    border-right: 4px solid transparent;
    border-left: 4px solid transparent;
    content: "";
    display: inline-block;
    height: 0;
    width: 0;
    vertical-align: middle;
 
    -webkit-transition: color 0.1s linear;
    -moz-transition: color 0.1s linear;
    -o-transition: color 0.1s linear;
      transition: color 0.1s linear;
}

nav > ul > li:hover {
  background-color: rgb( 40, 44, 47 );
}
 
nav > ul > li:hover > a {
  color: rgb( 255, 255, 255 );
}
 
nav > ul > li:hover > a > .caret {
  border-top-color: rgb( 255, 255, 255 );
  }
      </style>
      
      <h1 style="font-size:40px; text-align:center;">Spatio-temporal binary feature for collaborative mapping between multiple moving cameras with unkown initial poses</h1>
      
      <section>
      <h1>Summary</h1>
      <ul>
		  <li><a href="#mapavg">Map merging by averaging</a></li>
		  <li><a href="#mapfirst">Map merging by preserving the points of the reference camera</a></li>
		<li><a href="#fbk">The FBK Outdoor dataset</a></li>
		<li><a href="#dbow2">Overlap between sequences with Bag of Binary Words</a></li>
		<li><a href="#relatedwork">Related Work</a></li>
      </ul>
      </section>
      
     <hr>

   
<section>
 <h2>Setup</h2>
 <ul>
	 <li><a href="#fbk">FBK outdoor dataset</a></li>
	 <li>Image sequence length = 50 frames</li>
	 <li>Camera poses obtained using COLMAP, a SfM pipeline, using all the images from all cameras as input</li>
	 <li>Annotated poses are transformed by subtracting the initial pose such that each trajectory refers to its own local coordinate system (frame 0 is the origin of the local world)</li>
	 <li>Spatio-temporal features obtained by tracking ORB features with KLT</li>
	 <li>Distinctive descriptor (FUSION): vector of temporally dominant bits + vector of temporally stable bits (to use in the matching)</li>
	 <li>Each 3D point is reconstructed from the corresponding spatio-temporal feature by using the N-View triangulation approach (based on Singular Value Decomposition)</li>
	 <li>Global coordinate system = local coordinate system of camera 1</li>			 
 </ul>
</section>

<section>
	<h2>Filtering bad matches for estimating the similarity transformation</h2>
	<p>Descriptors of the spatio-temporal features (and corresponding 3D points) are matched with the FUSION approach</p>
	<p>The similarity transformation is estimated with the Horn's method for register two data points from one coordinatate system to the absolute system of the second datapoints (return orientation, position and scale)</p>
	<p>Horn's method is efficient, but requires known correspondonces, same number of data points and does not handle outliers (wrong matches affect the final estimation)</p>
	<p>Before introducing RANSAC, we filter out possible wrong matches by looking at their Euclidean distances in the two coordinate systems. We remove all those matches whose distance is larger than 1.5 the median distance of all 3D matches.</p>
	<p>The plot below show the histogram of the distances between matching points with a zoom between 0-100 for each camera pair. The histogram is normalised w.r.t. the total number of matches. Green denotes the surviving matches below the threshold, and red the discarded matches that are not use for the similarity estimation.</p>
	<p>This step will be remove once the similarity transformation is estimated within a RANSAC scheme (minimisation of the 3D error between matching points) that will separate outliers and inliers automatically.</p>
	<table id="eucdist" style="width=40%;border-collapse: collapse;margin: 0px auto;">
		<tr>
			<th style="text-align:center;">Camera ID</th>
			<th style="text-align:center;">Histograms</th>
			<th style="text-align:center;"># of matches</th>
			<th style="text-align:center;"># of inliers</th>
		</tr>
		<tr>
			<td style="text-align:left;">#2</td>
			<td style="text-align:center;"><img id="dbow12" src="coslam/fbk/matches_dist_0to1.png" alt="Smiley face" width="60%"></td>
			<td style="text-align:right;">178</td>
			<td style="text-align:right;">101</td>
		</tr>
		<tr>
			<td style="text-align:left;">#3</td>
			<td style="text-align:center;"><img id="dbow13" src="coslam/fbk/matches_dist_0to2.png" alt="Smiley face" width="60%"></td>
			<td style="text-align:right;">160</td>
			<td style="text-align:right;">106</td>
		</tr>
		<tr>
			<td style="text-align:left;">#5</td>
			<td style="text-align:center;"><img id="dbow15" src="coslam/fbk/matches_dist_0to4.png" alt="Smiley face" width="60%"></td>
			<td style="text-align:right;">111</td>
			<td style="text-align:right;">64</td>
		</tr>
	</table>
</section>
<br>
<hr>  
 
<section id="mapavg">
<h2>Map merging by averaging</h2>
<ul>
  <li>3D points independently reconstructed from two cameras are matched using the distinctive descriptor;</li>
  <li>Descriptor matching is performed pairwise between all cameras (except camera 1) with camera 1;</li>
  <li>3D points from other cameras are registered in the global reference frame using the estimated similarity transformation</li>
  <li>Matched 3D points are fused by <mark>averaging</mark> their 3D locations (might not be the best solution)</li>
</ul>

      
<div style="text-align:center;margin: 0px auto;"> 
  <video id="video1" width="1280" height="720" controls >
    <source src="fbk_globalmap_avg.webm" type="video/webm">
  </video>
  <br><br>
  <button onclick="makeBig()">Big</button>
  <button onclick="makeSmall()">Small</button>
  <button onclick="makeNormal()">Normal</button>
  <br>
  <p style="text-align:left;">
	  <strong>Video 1</strong>
		Global map reconstruction of 3D points (sparse) with camera poses and trajectories. The trajectories relative to the local coordinate system of each camera 
		(all of them start at [0,0,0]) are aligned according to the estimated similarity transformation with respect to the coordinate system of the first camera (blue).
		<br>
		The image sequence for each camera is shown with a bounding box whose color corresponds to the camera color in the plots on the bottom.
		The plot of the left shows the final global reconstruction with all points visualised with their corresponding RGB color. 
		By visualising the final reconstruction with their RGB colors, we can understand if the reconstruction can make sense with the scene seen from the images and 
		if the camera poses over time are consistent with the visualisation and looking at the right direction. 
		The plot on the right represents the same visualisation but the points are shown with the color of the camera that reconstructed that point. 
		Points that are fused are shown in magenta in this plot. 
    </p>
</div> 

<script> 
  var myVideo = document.getElementById("video1"); 

  function makeBig() { 
      myVideo.width = 1280; 
      myVideo.height = 720; 
  } 

  function makeSmall() { 
      myVideo.width = 320; 
      myVideo.height = 180; 
  } 

  function makeNormal() { 
      myVideo.width = 640;
      myVideo.height = 360;  
  } 
</script> 

  <br><br>
<figure style="text-align:center">
   <img id="imgtraj"src="trajectory_alignment_avg.png" alt="Smiley face" width="730">
   <figcaption id='traj'>Estimated trajectories before and after alignment. Filled circles are the final position of each camera. Black dotted lines are the trajectories annotated with the Structure from Motion pipeline COLMAP. Before alignment, all the cameras start at position (0,0,0). The relative motion between consecutive poses within a single camera correspond to the annotated one, as we only subtract the initial pose for each camera. Because of this, each trajectory is obtained with respect to the local coordinate system where the origin is the first frame of the camera. After reconstructing the map of 3D points (sparse) and the similarity transformation that undergoes between each camera and the reference one (e.g. the first camera in this example), the trajectories are aligned into the global coordinate system (here the coordinate system of the camera 1). In the current approach, RANSAC is not still used to remove outliers in the 3D matches and any optimisations such as bundle adjustment is performed once the trajectories and maps are aligned.</figcaption>
   <style>
   figcaption, traj {
   text-align:left;
   width:100%;
   }
   </style>
</figure>

<br><br><br>

  </div>
  
<div style="text-align:left">
<h3>Estimation of the translation error between camera pairs</h3>

<UL>
<LI>Let <!-- MATH
 $\mathbf{R} \in SO(3)$
 -->
<SPAN CLASS="MATH"><IMG
 WIDTH="88" HEIGHT="34" ALIGN="MIDDLE" BORDER="0"
 SRC="latex/img1.png"
 ALT="$\mathbf{R} \in SO(3)$"></SPAN> be a <SPAN CLASS="MATH"><IMG
 WIDTH="42" HEIGHT="31" ALIGN="MIDDLE" BORDER="0"
 SRC="latex/img2.png"
 ALT="$3\times3$"></SPAN> orientation matrix belonging to the Special Orthogonal group in 3D.
</LI>
<LI>Let <!-- MATH
 $\mathbf{t} \in \mathbb{R}^3$
 -->
<SPAN CLASS="MATH"><IMG
 WIDTH="54" HEIGHT="37" ALIGN="MIDDLE" BORDER="0"
 SRC="latex/img3.png"
 ALT="$\mathbf{t} \in \mathbb{R}^3$"></SPAN> be a column 3-dimensional vector belonging to the 3D Euclidean space.
</LI>
<LI>Let <!-- MATH
 $\mathbf{C} = \begin{bmatrix} \mathbf{R} &\mathbf{t} \\\mathbf{0} & 1 \end{bmatrix} \in SE(3)$
 -->
<SPAN CLASS="MATH"><IMG
 WIDTH="170" HEIGHT="73" ALIGN="MIDDLE" BORDER="0"
 SRC="latex/img4.png"
 ALT="$\mathbf{C} = \begin{bmatrix}\mathbf{R} &amp;\mathbf{t} \\ \mathbf{0} &amp; 1 \end{bmatrix} \in SE(3)$"></SPAN> be a <SPAN CLASS="MATH"><IMG
 WIDTH="43" HEIGHT="31" ALIGN="MIDDLE" BORDER="0"
 SRC="latex/img5.png"
 ALT="$4\times4$"></SPAN> matrix representing the 6DoF pose of camera viewpoint belonging to the Special Euclidean group in 3D (rigid body motion).
</LI>
<LI>Let <!-- MATH
 $\mathbf{C}_k^i$
 -->
<SPAN CLASS="MATH"><IMG
 WIDTH="26" HEIGHT="38" ALIGN="MIDDLE" BORDER="0"
 SRC="latex/img6.png"
 ALT="$\mathbf{C}_k^i$"></SPAN> and <!-- MATH
 $\mathbf{C}_k^j$
 -->
<SPAN CLASS="MATH"><IMG
 WIDTH="27" HEIGHT="42" ALIGN="MIDDLE" BORDER="0"
 SRC="latex/img7.png"
 ALT="$\mathbf{C}_k^j$"></SPAN> be the poses of camera <SPAN CLASS="MATH"><IMG
 WIDTH="10" HEIGHT="16" ALIGN="BOTTOM" BORDER="0"
 SRC="latex/img8.png"
 ALT="$i$"></SPAN> and camera <SPAN CLASS="MATH"><IMG
 WIDTH="13" HEIGHT="32" ALIGN="MIDDLE" BORDER="0"
 SRC="latex/img9.png"
 ALT="$j$"></SPAN> respectively at time <SPAN CLASS="MATH"><IMG
 WIDTH="13" HEIGHT="20" ALIGN="BOTTOM" BORDER="0"
 SRC="latex/img10.png"
 ALT="$k$"></SPAN>.
</LI>
<LI>Let <!-- MATH
 $\mathbf{\Delta}^{j\leftarrow i}_k = \mathbf{C}_k^i \left( \mathbf{C}_k^j\right)^{-1}$
 -->
<SPAN CLASS="MATH"><IMG
 WIDTH="157" HEIGHT="54" ALIGN="MIDDLE" BORDER="0"
 SRC="latex/img11.png"
 ALT="$\mathbf{\Delta}^{j\leftarrow i}_k = \mathbf{C}_k^i \left( \mathbf{C}_k^j\right)^{-1}$"></SPAN> be the relative rigid transformation between the pose of camera <SPAN CLASS="MATH"><IMG
 WIDTH="13" HEIGHT="32" ALIGN="MIDDLE" BORDER="0"
 SRC="latex/img9.png"
 ALT="$j$"></SPAN> and the pose of camera <SPAN CLASS="MATH"><IMG
 WIDTH="10" HEIGHT="16" ALIGN="BOTTOM" BORDER="0"
 SRC="latex/img8.png"
 ALT="$i$"></SPAN> at time <SPAN CLASS="MATH"><IMG
 WIDTH="13" HEIGHT="20" ALIGN="BOTTOM" BORDER="0"
 SRC="latex/img10.png"
 ALT="$k$"></SPAN>.
</LI>
<LI>Let <!-- MATH
 $\tilde{\mathbf{C}}_k^j$
 -->
<SPAN CLASS="MATH"><IMG
 WIDTH="26" HEIGHT="42" ALIGN="MIDDLE" BORDER="0"
 SRC="latex/img12.png"
 ALT="$\tilde{\mathbf{C}}_k^j$"></SPAN> be the estimated pose of camera <SPAN CLASS="MATH"><IMG
 WIDTH="13" HEIGHT="32" ALIGN="MIDDLE" BORDER="0"
 SRC="latex/img9.png"
 ALT="$j$"></SPAN> at time <SPAN CLASS="MATH"><IMG
 WIDTH="13" HEIGHT="20" ALIGN="BOTTOM" BORDER="0"
 SRC="latex/img10.png"
 ALT="$k$"></SPAN> after align all the poses according to the estimated similarity transformation.
</LI>
<LI>Let <!-- MATH
 $\tilde{\mathbf{\Delta}}^{j\leftarrow i}_k = \mathbf{C}_k^i \left( \tilde{\mathbf{C}}_k^j\right)^{-1}$
 -->
<SPAN CLASS="MATH"><IMG
 WIDTH="157" HEIGHT="54" ALIGN="MIDDLE" BORDER="0"
 SRC="latex/img13.png"
 ALT="$\tilde{\mathbf{\Delta}}^{j\leftarrow i}_k = \mathbf{C}_k^i \left( \tilde{\mathbf{C}}_k^j\right)^{-1}$"></SPAN> be the relative rigid transformation between the estimated, aligned pose of camera <SPAN CLASS="MATH"><IMG
 WIDTH="13" HEIGHT="32" ALIGN="MIDDLE" BORDER="0"
 SRC="latex/img9.png"
 ALT="$j$"></SPAN> and the pose of camera <SPAN CLASS="MATH"><IMG
 WIDTH="10" HEIGHT="16" ALIGN="BOTTOM" BORDER="0"
 SRC="latex/img8.png"
 ALT="$i$"></SPAN> at time <SPAN CLASS="MATH"><IMG
 WIDTH="13" HEIGHT="20" ALIGN="BOTTOM" BORDER="0"
 SRC="latex/img10.png"
 ALT="$k$"></SPAN>.
</LI>
<LI>The relative error is <!-- MATH
 $\varepsilon^{j\leftarrow i}_k = \mathbf{\Delta}^{j\leftarrow i}_k \left( \tilde{\mathbf{\Delta}}^{j\leftarrow i}_k \right)^{-1}$
 -->
<SPAN CLASS="MATH"><IMG
 WIDTH="186" HEIGHT="54" ALIGN="MIDDLE" BORDER="0"
 SRC="latex/img14.png"
 ALT="$\varepsilon^{j\leftarrow i}_k = \mathbf{\Delta}^{j\leftarrow i}_k \left( \tilde{\mathbf{\Delta}}^{j\leftarrow i}_k \right)^{-1}$"></SPAN>.
</LI>
<LI>The translation error is given by the norm of the translation part of <!-- MATH
 $\varepsilon^{j\leftarrow i}_k$
 -->
<SPAN CLASS="MATH"><IMG
 WIDTH="38" HEIGHT="42" ALIGN="MIDDLE" BORDER="0"
 SRC="latex/img15.png"
 ALT="$\varepsilon^{j\leftarrow i}_k$"></SPAN>.
</LI>
<LI>As the trajectories are the same minus the alignment error, we compute the translation error only between the poses at the first frame <!-- MATH
 $\varepsilon^{j\leftarrow i}_0 = \mathbf{\Delta}^{j\leftarrow i}_0 \left( \tilde{\mathbf{\Delta}}^{j\leftarrow i}_0 \right)^{-1}$
 -->
<SPAN CLASS="MATH"><IMG
 WIDTH="186" HEIGHT="54" ALIGN="MIDDLE" BORDER="0"
 SRC="latex/img16.png"
 ALT="$\varepsilon^{j\leftarrow i}_0 = \mathbf{\Delta}^{j\leftarrow i}_0 \left( \tilde{\mathbf{\Delta}}^{j\leftarrow i}_0 \right)^{-1}$"></SPAN> for each camera pair with <SPAN CLASS="MATH"><IMG
 WIDTH="42" HEIGHT="16" ALIGN="BOTTOM" BORDER="0"
 SRC="latex/img17.png"
 ALT="$i=1$"></SPAN> and <!-- MATH
 $j=2, \ldots, 4$
 -->
<SPAN CLASS="MATH"><IMG
 WIDTH="91" HEIGHT="32" ALIGN="MIDDLE" BORDER="0"
 SRC="latex/img18.png"
 ALT="$j=2, \ldots, 4$"></SPAN>.
</LI>
</UL>
 </div> 

<br>

<div>
  <style>
  .transerr {
      width:40%;
      float: center;
  }
  .transerr, th, td {
      border: 1px solid black;
      border-collapse: collapse;
  }
  .transerr, td {
      padding: 15px;
      text-align: right;
  }
  .transerr#t01 tr:nth-child(even) {
      background-color: #eee;
  }
  .transerr#t01 tr:nth-child(odd) {
     background-color: #fff;
  }
  .transerr#t01 th {
      background-color: black;
      color: white;
  }
  </style> 
 <table id="transerr" style="border-collapse: collapse;margin: 0px auto;">
	<caption align="bottom"><mark>Tab.1</mark> Translation error for each camera pair before and after the alignment with the estimated similarity transformation. </caption>
  <tr>
    <th>Camera pair</th>
    <th>Before alignment</th>
    <th>After alignment</th>
  </tr>
  <tr>
    <td>1-2</td>
    <td>4.54</td>
    <td>0.49</td>
  </tr>
  <tr>
    <td>1-3</td>
    <td>7.32</td>
    <td>1.36</td>
  </tr>
    <tr>
    <td>1-5</td>
    <td>4.35</td>
    <td>1.12</td>
  </tr>
  
</table>
</div>
</section>

<hr>

<section id="mapfirst">      
            <h1>Map merging by preserving the points of the reference camera</h1>
            In this example, we removed the averaging operation when fusing matching points and we replace it with the preserving of the 3D location of the point in the reference camera. Even if the 3D points that have been fused seem less sparse around the scene, we got larger errors in the alignment of the trajectories. 
            <p></p>
            <div style="text-align:center"> 
  <video id="video2" width="1280" height="720" controls>
    <source src="fbk_globalmap.webm" type="video/webm">
  </video>
  <br><br>
  <button onclick="makeBig()">Big</button>
  <button onclick="makeSmall()">Small</button>
  <button onclick="makeNormal()">Normal</button>
</div> 

<script> 
  var myVideo = document.getElementById("video2"); 

  function makeBig() { 
      myVideo.width = 1280; 
      myVideo.height = 720; 
  } 

  function makeSmall() { 
      myVideo.width = 320; 
      myVideo.height = 180; 
  } 

  function makeNormal() { 
      myVideo.width = 640;
      myVideo.height = 360;  
  } 
</script> 

  <br><br>
<figure style="text-align:center">
   <img id="imgtraj"src="trajectory_alignment.png" alt="Smiley face" width="730">
   <figcaption id='traj'>Estimated trajectories before and after alignment. Filled circles are the final position of each camera. Black dotted lines are the trajectories annotated with the Structure from Motion pipeline COLMAP. Before alignment, all the cameras start at position (0,0,0). The relative motion between consecutive poses within a single camera correspond to the annotated one, as we only subtract the initial pose for each camera. Because of this, each trajectory is obtained with respect to the local coordinate system where the origin is the first frame of the camera. After reconstructing the map of 3D points (sparse) and the similarity transformation that undergoes between each camera and the reference one (e.g. the first camera in this example), the trajectories are aligned into the global coordinate system (here the coordinate system of the camera 1). In the current approach, RANSAC is not still used to remove outliers in the 3D matches and any optimisations such as bundle adjustment is performed once the trajectories and maps are aligned.</figcaption>
   <style>
   figcaption, traj {
   text-align:left;
   width:100%;
   }
   </style>
</figure>

<br><br><br><br><br><br><br><br>

<!--
<div lang="latex">
	R \in SO(3)
	<ul>
		<li>R \in SO(3)</li>
		<li>t \in \mathbf{R}^(3)</li>
		<li>C \in SE(3)</li>li
		<li>C_0^1 = \begin{brmatrix} R_0^1 & t_0^1 \\ 0 & 1 \end{brmatrix}</li>
	</ul>
</div>-->

<div  style="text-align:center">
  <style>
  .transerr {
      width:40%;
      float: center;
  }
  .transerr, th, td {
      border: 1px solid black;
      border-collapse: collapse;
  }
  th, td {
      padding: 15px;
      text-align: right;
  }
  .transerr#t01 tr:nth-child(even) {
      background-color: #eee;
  }
  .transerr#t01 tr:nth-child(odd) {
     background-color: #fff;
  }
  .transerr#t01 th {
      background-color: black;
      color: white;
  }
    caption {
    text-align:left;
}
  </style>
 <table id="transerr">
 <caption>Translation error for each camera pairs before and after the alignment with the estimated similarity transformation. </caption>
  <tr>
    <th>Camera pair</th>
    <th>Before alignment</th>
    <th>After alignment</th>
  </tr>
  <tr>
    <td>1-2</td>
    <td>4.54</td>
    <td>1.36</td>
  </tr>
  <tr>
    <td>1-3</td>
    <td>7.32</td>
    <td>2.99</td>
  </tr>
    <tr>
    <td>1-5</td>
    <td>4.35</td>
    <td>1.99</td>
  </tr>
</table>
</div>
</section>

<br><hr><br>

<section id="fbk">
	<h2>FBK Outdoor dataset</h2>
	<p>A set of 5 video sequences recorded using one single hand-held compact camera (Panasonic Lumix) in consecutive sessions outside the FBK building at near sunset time. <br>
	In these experiments, the camera 4 is not used and we consider only the first 50 frames. <br>
	The sequences are capturing the same scene starting from different viewpoints and under different challenging motions.</p>
	<table id="fbkcams" style="border-collapse: collapse;margin: 0px auto;">
		<tr><th>Camera ID</th><th>Filename</th><th>Date</th><th>Time</th><th>Duration</th></tr>
		<tr><td>#1</td><td>P1060024.MOV</td><td>2018:06:20</td><td>19:28:27</td><td>11.00s</td></tr>
		<tr><td>#2</td><td>P1060025.MOV</td><td>2018:06:20</td><td>19:29:07</td><td>15.00s</td></tr>
		<tr><td>#3</td><td>P1060026.MOV</td><td>2018:06:20</td><td>19:29:50</td><td>16.00s</td></tr>
		<tr><td>#4</td><td>P1060027.MOV</td><td>2018:06:20</td><td>19:30:49</td><td>11.50s</td></tr>
		<tr><td>#5</td><td>P1060028.MOV</td><td>2018:06:20</td><td>19:32:13</td><td>12.50s</td></tr>
	</table>
	<h3>Camera poses annotation</h3>
	<p>First 100 frames of each sequence are provided as input to COLMAP (a SfM+MVS pipeline for sparse and dense 3D reconstruction).</p>
	<p>COLMAP returns the estimated camera poses (6DoF) up to a scale factor.</p>
	<p>The estimated camera poses are used as reference and annotations for our approach.</p>
	
	<h3>Description</h3>
	<p>
		The FBK Outdoor dataset consists of five sequences acquired with a hand-held Panasonic LUMIX DMC-FP3 camera in multiple sessions in front of the FBK building. 
		During the acquisitions, there was no motions of the objects and there were no people moving around. 
		Each sequence is recorded starting from a different position and with different motions with respect to the scene, e.g. forward motion or left-to-right motion from the observer point of view. 
		The initial different camera poses and different motions introduce challenges such as viewpoint, scale, and illumination changes within each sequence and between sequences. 
		All the sequences are recorded at 30 fps with a narrow field of view. Sequence 1--5 consist of 330, 450, 480, 345, 375 frames, respectively. 
		From each sequence we select the first 100 frames after down-sampling the video at 10 fps. 
		To obtain calibration data (i.e. focal length, principal point and one radial distortion coefficient) and camera poses, we use COLMAP as a recent Structure-from-Motion pipeline, 
		which provides also the 3D reconstruction of the scene. As an off-line method that considers all the images as inputs, we rely on the camera poses and calibration information 
		as a reference for our evaluation. For our experiments, all the images are undistorted and rectified keeping the same resolutions, i.e. 1280x720 pixels.
	</p>
</section>

<br>
<hr>
<br>

<section id="dbow2">
	<h2>Bag of Binary Words</h2>
	<p>The approach of Bag of Binary Words (<a href="https://drive.google.com/file/d/12-kzwrIN98drqJEgPyikJ1dFarwdrasr/view">DBoW2</a>) using ORB features is used to compute a feature vector for each image. <br>
	We then compare and compute the similarity score for each image pair between two image sequences as well as the number of matches using the nearest neighbour approach with distance ratio test (threshold set to 50) between ORB features of the matched leaves in the visual words. <br>
	The image sequence of all the other cameras are compared against the image sequence of the reference camera (in this case, camera 1).<br>
	The number of ORB features exracted for each image is 2000.</p>
	
	<ul>
		<li>The similarity score is lower than 0.1 between all image pairs;</li>
		<li>Similarity score and matching score show similar patterns, but the matching score is more discriminative between images;</li>
		<li>In the second table we can notice that there is no direct correspondence between the best pair with the similarity score and matching score (especially for camera 3 and 4);</li>
		<li>The number of matches for camera 3 and 4 are relatively low;</li>
		<li>I need to understand the impact on the similarity transformation with the BoW in this case and if it fail given this number of matches;</li>
	</ul>
	
	<br>

	<style>
	#overlap {
	  width:100%;
	  overflow-x:auto;
	  border-collapse: collapse;
	}
	#overlap th {
  	text-align:center;
	}
	</style>
	
	
	<table id="overlap">
		<tr>
			<th>Camera ID</th>
			<th>DBoW2 score (0-1)</th>
			<th>DboW2 score normalized</th>
			<th># of matches (normalised)</th>
		</tr>
		<tr>
			<td>#2</td>
			<td><img id="dbow12" src="coslam/fbk/dbow2_score_12_fbk.png" alt="Smiley face" width="100%"></td>
			<td><img id="dbown12" src="coslam/fbk/dbow2_score_norm_12_fbk.png" alt="Smiley face" width="100%"></td>
			<td><img id="matchscore12" src="coslam/fbk/matching_score_12_fbk.png" alt="Smiley face" width="100%"></td>
		</tr>
		<tr>
			<td>#3</td>
			<td><img id="dbow13" src="coslam/fbk/dbow2_score_13_fbk.png" alt="Smiley face" width="100%"></td>
			<td><img id="dbown13" src="coslam/fbk/dbow2_score_norm_13_fbk.png" alt="Smiley face" width="100%"></td>
			<td><img id="matchscore13" src="coslam/fbk/matching_score_13_fbk.png" alt="Smiley face" width="100%"></td>
		</tr>
		<tr>
			<td>#4</td>
			<td><img id="dbow14" src="coslam/fbk/dbow2_score_14_fbk.png" alt="Smiley face" width="100%"></td>
			<td><img id="dbown14" src="coslam/fbk/dbow2_score_norm_14_fbk.png" alt="Smiley face" width="100%"></td>
			<td><img id="matchscore14" src="coslam/fbk/matching_score_14_fbk.png" alt="Smiley face" width="100%"></td>
		</tr>
		<tr>
			<td>#5</td>
			<td><img id="dbow15" src="coslam/fbk/dbow2_score_15_fbk.png" alt="Smiley face" width="100%"></td>
			<td><img id="dbown15" src="coslam/fbk/dbow2_score_norm_15_fbk.png" alt="Smiley face" width="100%"></td>
			<td><img id="matchscore15" src="coslam/fbk/matching_score_15_fbk.png" alt="Smiley face" width="100%"></td>
		</tr>
	</table>
	
	<br>
	<table id="overlapbest" style="border-collapse: collapse;margin: 0px auto;">
		<tr>
			<th style="text-align:center;">Camera ID</th>
			<th style="text-align:center;">Best image pair (sim score)</th>
			<th style="text-align:center;">Best image pair (match score)</th>
		</tr>
		<tr>
			<td style="text-align:left;">#2</td>
			<td style="text-align:left;">(48,81) -> 0.057266 (551 matches)</td>
			<td style="text-align:left;">(49,81) -> 0.055533 (568 matches)</td>
		</tr>
		<tr>
			<td style="text-align:left;">#3</td>
			<td style="text-align:left;">(69,88) -> 0.020176 (45 matches)</td>
			<td style="text-align:left;">(81,100) -> 0.012078 (76 matches)</td>
		</tr>
		<tr>
			<td style="text-align:left;">#4</td>
			<td style="text-align:left;">(54,95) -> 0.02932 (107 matches)</td>
			<td style="text-align:left;">(86,88) -> 0.020649 (181 matches)</td>
		</tr>
		<tr>
			<td style="text-align:left;">#5</td>
			<td style="text-align:left;">(97,15) -> 0.066271 (511 matches)</td>
			<td style="text-align:left;">(98,17) -> 0.061309 (534 matches)</td>
		</tr>
	</table>
	
</section>
 
    

<br>  
<hr>
<br>

<section id="relatedwork">
<h2>Related work</h2>
<ul>
	<li>CoSLAM</li>
	<li>CSfM</li>
	<li>CORB-SLAM</li>
	<li>C<sup>2</sup>TAM</li>
	<li>Multi-UAV Collaborative Monocular SLAM</li>
	<li></li>
</ul>
</section>      

<br>

<p><a href="matchesgal.php">Matches</a></p>
<!--
<form class = "form-signin" role = "form" 
	action = "matchesgal.php"
	method = "POST">
	<button class = "btn btn-lg btn-primary btn-block" type = "submit" 
		name = "logout">Matches</button>
</form>-->

   </body>
</html>
