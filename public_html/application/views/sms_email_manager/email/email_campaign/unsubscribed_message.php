<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link rel="stylesheet" href="<?php echo base_url('plugins/alertifyjs/css/alertify.min.css')?>" />
<link rel="stylesheet" href="<?php echo base_url('plugins/alertifyjs/css/themes/default.min.css')?>" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/izitoast/css/iziToast.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/izitoast/css/iziToast.min.css">


<style>
	body {
		width: 100vw;
		height: 100vh;
		background: #fefefe;
		color: #373737;
		margin: 0;
		padding: 0;
		background: #543093;
		background: -moz-linear-gradient(45deg, #543093 32%, #d960ae 100%);
		background: -webkit-linear-gradient(45deg, #543093 32%, #d960ae 100%);
		background: linear-gradient(45deg, #543093 32%, #d960ae 100%);
		filter: progid:DXImageTransform.Microsoft.gradient(
				startColorstr="#543093",
				endColorstr="#d960ae",
				GradientType=1
			);
	}
	* {
		box-sizing: border-box;
		font-family: "Cabin", Arial, sans-serif;
	}
	.container {
		width: 600px;
		-webkit-transform: translate(-50%, -50%);
		transform: translate(-50%, -50%);
		top: 50%;
		left: 50%;
		margin: 0;
		position: absolute;
		background: #fefefe;
		display: flex;
		align-items: center;
		justify-content: flex-start;
		flex-direction: column;
		padding-bottom:30px;
		box-shadow: 5px 10px 40px 0 rgba(0, 0, 0, 0.2);
		border-radius: 5px;
	}


	svg {
		max-width: 90%;
		position: relative;
		left: 5%;
		margin: 0 auto;
	}

	.bottom {
		text-align: center;
		margin-top: 0em;
		/*max-width: 70%;*/
		position: relative;
		margin: 0 auto;
		h2 {
			font-family: "Rokkitt", sans-serif;
			letter-spacing: 0.05em;
			font-size: 30px;
			line-height: 1.2;
			text-align: center;
			margin: 0 auto 0.25em;
		}
		p {
			color: #777;
			letter-spacing: 0.1em;
			font-size: 16px;
			line-height: 1.4;
			margin: 0 auto 2em;
		}
	}

	.buttons {
		width: 100%;
		display: flex;
		justify-content: center;
		flex-wrap: wrap;
		align-items: center;
		button {
			padding: 10px 30px;
			font-size: 20px;
			background-color: #d960ae;
			border: 0;
			cursor: pointer;
			border-radius: 4px;
			letter-spacing: 0.1em;
			color: #ffffff;
			margin-right: 20px;
			margin-bottom: 15px;
			transition: all 0.25s ease-in-out;
			&:hover {
				background-color: darken(#d960ae, 10%);
			}
			&#cancel {
				margin-right: 0;
				color: #333;
				background-color: #eddfeb;
				&:hover {
					background-color: darken(#eddfeb, 10%);
				}
			}
			&#go-back {
				display: none;
			}
			&:focus {
				border: none;
				outline: 0;
			}
		}
	}
	.buttons .btn {margin:0 10px;}
	#blob-3,
	#blob-2,
	#mouth-happy,
	#mouth-sad,
	#eyebrow-happy-left,
	#eyebrow-happy-right,
	#eyes-laughing,
	#open-mouth,
	#tongue,
	#mouth-scared {
		display: none;
	}
	@media (max-width: 699px) {
		.container {
			width: 90%;
		}
			.bottom {
			margin-top: 1em;
				max-width:90%;
		}
	}
	@media (max-width: 399px) {
		.container {
			padding: 20px;
		}
		.bottom {
			// margin-top: 0em;
			// max-width: 90%;
			h2 {
				font-size: 24px;
			}
		}
		.buttons {
			flex-direction: column;
			button {
				margin-right: 0;
			}
		}
		svg {
			padding-top: 0;
		}
	}

</style>


<body>
	<div class="container">
		<div class="inner-container">
			<svg id="svg" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 590 484.7">
				<g id="blobs">
					<path id="blob-1" d="M545.5,299c0,80.3-28.6,150.4-126.4,139.4-63.2-7.1-109.3-37.3-142.6-37.3-45.7,0-105.4,29.3-146.8,22.2-69-11.7-85.3-66.8-85.3-135.8,0-56.3,25.5-99.9,46.2-140.8,18.3-36.1,55.9-97.8,125.1-100.5,53.3-2.1,97.4,50.5,138.4,74.2,49.9,28.8,98.4-1.8,126,1.3C537.9,127.9,545.5,265.5,545.5,299Z" fill="#eddfeb"/>
					<path id="blob-3" d="M55.1,300.7c0,80.3,27.4,150.4,121,139.4,60.5-7.1,104.7-37.3,136.5-37.3,43.8,0,100.9,29.3,140.5,22.2,66-11.7,81.7-66.8,81.7-135.8,0-56.3-16.2-103.6-36.1-144.5-17.6-36.1-54.9-97.4-121.2-100.1-51-2.1-100.1,53.8-139.4,77.5-47.8,28.8-94.3-1.8-120.7,1.3C62.4,129.6,55.1,267.1,55.1,300.7Z" fill="#eddfeb"/>
				</g>
				<g id="confetti" class="confetti">
					<rect x="284" y="230.4" width="17.7" height="17.67" rx="4" ry="4" fill="#543093"/>
					<rect x="284" y="230.4" width="17.7" height="17.67" rx="4" ry="4" fill="#543093"/>
					<rect x="285.4" y="231.7" width="17.7" height="17.67" rx="4" ry="4" fill="#fff"/>
					<rect x="285.4" y="231.7" width="17.7" height="17.67" rx="4" ry="4" fill="#fff"/>
					<rect x="285.4" y="230.1" width="17.7" height="17.67" rx="4" ry="4" fill="#d960ae"/>
					<rect x="285.4" y="230.1" width="17.7" height="17.67" rx="4" ry="4" fill="#d960ae"/>
					<rect x="285.4" y="231.7" width="17.7" height="17.67" rx="4" ry="4" fill="#f3c1df"/>
					<rect x="285.4" y="231.7" width="17.7" height="17.67" rx="4" ry="4" fill="#f3c1df"/>
					<circle cx="294.1" cy="241.3" r="9.7" fill="#543093"/>
					<circle cx="294.1" cy="243.6" r="12" fill="none" stroke="#fff" stroke-miterlimit="10" stroke-width="2"/>
					<circle cx="294.2" cy="243.6" r="12" fill="#fff"/>
					<circle cx="294.2" cy="243.6" r="12" fill="none" stroke="#fff" stroke-miterlimit="10" stroke-width="2"/>
					<circle cx="294.2" cy="243.6" r="12" fill="none" stroke="#d960ae" stroke-miterlimit="10" stroke-width="2"/>
					<circle cx="294.2" cy="243.6" r="12" fill="none" stroke="#d960ae" stroke-miterlimit="10" stroke-width="2"/>
					<circle cx="295.9" cy="242.1" r="12" fill="none" stroke="#543093" stroke-miterlimit="10" stroke-width="2"/>
					<circle cx="295.9" cy="242.1" r="12" fill="none" stroke="#543093" stroke-miterlimit="10" stroke-width="2"/>
					<circle cx="294.1" cy="241.3" r="9.7" fill="#d960ae"/>
					<circle cx="294.1" cy="241.3" r="9.7" fill="#d960ae"/>
					<circle cx="292.9" cy="241.3" r="9.7" fill="#fff"/>
					<circle cx="294.1" cy="241.3" r="9.7" fill="#d960ae"/>
					<circle cx="294.1" cy="241.3" r="9.7" fill="#543093"/>
					<circle cx="294.1" cy="241.3" r="9.7" fill="#d960ae"/>
					<circle cx="294.1" cy="241.3" r="9.7" fill="#543093"/>
					<circle cx="294.1" cy="241.3" r="9.7" fill="#543093"/>
					<path d="M300.9,243.2l-3-3a1.5,1.5,0,0,1,0-2.1l3-3a2,2,0,0,0,.3-2.5,1.9,1.9,0,0,0-2.9-.3l-3.1,3.1a1.5,1.5,0,0,1-2.1,0l-3-3a2,2,0,0,0-2.5-.3,1.9,1.9,0,0,0-.3,2.9l3.1,3.1a1.5,1.5,0,0,1,0,2.1l-3,3a2,2,0,0,0-.3,2.5,1.9,1.9,0,0,0,2.9.3l3.1-3.1a1.5,1.5,0,0,1,2.1,0l3.1,3.1a1.9,1.9,0,0,0,2.9-.3A2,2,0,0,0,300.9,243.2Z" fill="#f3c1df"/>
					<path d="M300.9,243.2l-3-3a1.5,1.5,0,0,1,0-2.1l3-3a2,2,0,0,0,.3-2.5,1.9,1.9,0,0,0-2.9-.3l-3.1,3.1a1.5,1.5,0,0,1-2.1,0l-3-3a2,2,0,0,0-2.5-.3,1.9,1.9,0,0,0-.3,2.9l3.1,3.1a1.5,1.5,0,0,1,0,2.1l-3,3a2,2,0,0,0-.3,2.5,1.9,1.9,0,0,0,2.9.3l3.1-3.1a1.5,1.5,0,0,1,2.1,0l3.1,3.1a1.9,1.9,0,0,0,2.9-.3A2,2,0,0,0,300.9,243.2Z" fill="#543093"/>
					<path d="M300.9,243.2l-3-3a1.5,1.5,0,0,1,0-2.1l3-3a2,2,0,0,0,.3-2.5,1.9,1.9,0,0,0-2.9-.3l-3.1,3.1a1.5,1.5,0,0,1-2.1,0l-3-3a2,2,0,0,0-2.5-.3,1.9,1.9,0,0,0-.3,2.9l3.1,3.1a1.5,1.5,0,0,1,0,2.1l-3,3a2,2,0,0,0-.3,2.5,1.9,1.9,0,0,0,2.9.3l3.1-3.1a1.5,1.5,0,0,1,2.1,0l3.1,3.1a1.9,1.9,0,0,0,2.9-.3A2,2,0,0,0,300.9,243.2Z" fill="#d960ae"/>
					<path d="M300.9,243.2l-3-3a1.5,1.5,0,0,1,0-2.1l3-3a2,2,0,0,0,.3-2.5,1.9,1.9,0,0,0-2.9-.3l-3.1,3.1a1.5,1.5,0,0,1-2.1,0l-3-3a2,2,0,0,0-2.5-.3,1.9,1.9,0,0,0-.3,2.9l3.1,3.1a1.5,1.5,0,0,1,0,2.1l-3,3a2,2,0,0,0-.3,2.5,1.9,1.9,0,0,0,2.9.3l3.1-3.1a1.5,1.5,0,0,1,2.1,0l3.1,3.1a1.9,1.9,0,0,0,2.9-.3A2,2,0,0,0,300.9,243.2Z" fill="#f3c1df"/>
					<path d="M300.9,243.2l-3-3a1.5,1.5,0,0,1,0-2.1l3-3a2,2,0,0,0,.3-2.5,1.9,1.9,0,0,0-2.9-.3l-3.1,3.1a1.5,1.5,0,0,1-2.1,0l-3-3a2,2,0,0,0-2.5-.3,1.9,1.9,0,0,0-.3,2.9l3.1,3.1a1.5,1.5,0,0,1,0,2.1l-3,3a2,2,0,0,0-.3,2.5,1.9,1.9,0,0,0,2.9.3l3.1-3.1a1.5,1.5,0,0,1,2.1,0l3.1,3.1a1.9,1.9,0,0,0,2.9-.3A2,2,0,0,0,300.9,243.2Z" fill="#fff"/>
					<path d="M300.9,243.2l-3-3a1.5,1.5,0,0,1,0-2.1l3-3a2,2,0,0,0,.3-2.5,1.9,1.9,0,0,0-2.9-.3l-3.1,3.1a1.5,1.5,0,0,1-2.1,0l-3-3a2,2,0,0,0-2.5-.3,1.9,1.9,0,0,0-.3,2.9l3.1,3.1a1.5,1.5,0,0,1,0,2.1l-3,3a2,2,0,0,0-.3,2.5,1.9,1.9,0,0,0,2.9.3l3.1-3.1a1.5,1.5,0,0,1,2.1,0l3.1,3.1a1.9,1.9,0,0,0,2.9-.3A2,2,0,0,0,300.9,243.2Z" fill="#543093"/>
					<path d="M300.9,243.1l-3-3a1.5,1.5,0,0,1,0-2.1l3-3a2,2,0,0,0,.3-2.5,1.9,1.9,0,0,0-2.9-.3l-3.1,3.1a1.5,1.5,0,0,1-2.1,0l-3-3a2,2,0,0,0-2.5-.3,1.9,1.9,0,0,0-.3,2.9l3.1,3.1a1.5,1.5,0,0,1,0,2.1l-3,3a2,2,0,0,0-.3,2.5,1.9,1.9,0,0,0,2.9.3l3.1-3.1a1.5,1.5,0,0,1,2.1,0l3.1,3.1a1.9,1.9,0,0,0,2.9-.3A2,2,0,0,0,300.9,243.1Z" fill="#d960ae"/>
					<path d="M300.9,243.1l-3-3a1.5,1.5,0,0,1,0-2.1l3-3a2,2,0,0,0,.3-2.5,1.9,1.9,0,0,0-2.9-.3l-3.1,3.1a1.5,1.5,0,0,1-2.1,0l-3-3a2,2,0,0,0-2.5-.3,1.9,1.9,0,0,0-.3,2.9l3.1,3.1a1.5,1.5,0,0,1,0,2.1l-3,3a2,2,0,0,0-.3,2.5,1.9,1.9,0,0,0,2.9.3l3.1-3.1a1.5,1.5,0,0,1,2.1,0l3.1,3.1a1.9,1.9,0,0,0,2.9-.3A2,2,0,0,0,300.9,243.1Z" fill="#f3c1df"/>
					<path d="M300.9,243.2l-3-3a1.5,1.5,0,0,1,0-2.1l3-3a2,2,0,0,0,.3-2.5,1.9,1.9,0,0,0-2.9-.3l-3.1,3.1a1.5,1.5,0,0,1-2.1,0l-3-3a2,2,0,0,0-2.5-.3,1.9,1.9,0,0,0-.3,2.9l3.1,3.1a1.5,1.5,0,0,1,0,2.1l-3,3a2,2,0,0,0-.3,2.5,1.9,1.9,0,0,0,2.9.3l3.1-3.1a1.5,1.5,0,0,1,2.1,0l3.1,3.1a1.9,1.9,0,0,0,2.9-.3A2,2,0,0,0,300.9,243.2Z" fill="#543093"/>
					<path d="M300.9,243.2l-3-3a1.5,1.5,0,0,1,0-2.1l3-3a2,2,0,0,0,.3-2.5,1.9,1.9,0,0,0-2.9-.3l-3.1,3.1a1.5,1.5,0,0,1-2.1,0l-3-3a2,2,0,0,0-2.5-.3,1.9,1.9,0,0,0-.3,2.9l3.1,3.1a1.5,1.5,0,0,1,0,2.1l-3,3a2,2,0,0,0-.3,2.5,1.9,1.9,0,0,0,2.9.3l3.1-3.1a1.5,1.5,0,0,1,2.1,0l3.1,3.1a1.9,1.9,0,0,0,2.9-.3A2,2,0,0,0,300.9,243.2Z" fill="#d960ae"/>
					<path d="M300.9,243.2l-3-3a1.5,1.5,0,0,1,0-2.1l3-3a2,2,0,0,0,.3-2.5,1.9,1.9,0,0,0-2.9-.3l-3.1,3.1a1.5,1.5,0,0,1-2.1,0l-3-3a2,2,0,0,0-2.5-.3,1.9,1.9,0,0,0-.3,2.9l3.1,3.1a1.5,1.5,0,0,1,0,2.1l-3,3a2,2,0,0,0-.3,2.5,1.9,1.9,0,0,0,2.9.3l3.1-3.1a1.5,1.5,0,0,1,2.1,0l3.1,3.1a1.9,1.9,0,0,0,2.9-.3A2,2,0,0,0,300.9,243.2Z" fill="#f3c1df"/>
					<path d="M300.9,243.2l-3-3a1.5,1.5,0,0,1,0-2.1l3-3a2,2,0,0,0,.3-2.5,1.9,1.9,0,0,0-2.9-.3l-3.1,3.1a1.5,1.5,0,0,1-2.1,0l-3-3a2,2,0,0,0-2.5-.3,1.9,1.9,0,0,0-.3,2.9l3.1,3.1a1.5,1.5,0,0,1,0,2.1l-3,3a2,2,0,0,0-.3,2.5,1.9,1.9,0,0,0,2.9.3l3.1-3.1a1.5,1.5,0,0,1,2.1,0l3.1,3.1a1.9,1.9,0,0,0,2.9-.3A2,2,0,0,0,300.9,243.2Z" fill="#fff"/>
					<path d="M300.9,243.1l-3-3a1.5,1.5,0,0,1,0-2.1l3-3a2,2,0,0,0,.3-2.5,1.9,1.9,0,0,0-2.9-.3l-3.1,3.1a1.5,1.5,0,0,1-2.1,0l-3-3a2,2,0,0,0-2.5-.3,1.9,1.9,0,0,0-.3,2.9l3.1,3.1a1.5,1.5,0,0,1,0,2.1l-3,3a2,2,0,0,0-.3,2.5,1.9,1.9,0,0,0,2.9.3l3.1-3.1a1.5,1.5,0,0,1,2.1,0l3.1,3.1a1.9,1.9,0,0,0,2.9-.3A2,2,0,0,0,300.9,243.1Z" fill="#d960ae"/>
					<path d="M300.9,243.1l-3-3a1.5,1.5,0,0,1,0-2.1l3-3a2,2,0,0,0,.3-2.5,1.9,1.9,0,0,0-2.9-.3l-3.1,3.1a1.5,1.5,0,0,1-2.1,0l-3-3a2,2,0,0,0-2.5-.3,1.9,1.9,0,0,0-.3,2.9l3.1,3.1a1.5,1.5,0,0,1,0,2.1l-3,3a2,2,0,0,0-.3,2.5,1.9,1.9,0,0,0,2.9.3l3.1-3.1a1.5,1.5,0,0,1,2.1,0l3.1,3.1a1.9,1.9,0,0,0,2.9-.3A2,2,0,0,0,300.9,243.1Z" fill="#f3c1df"/>
				</g>
				<g id="envelope">
					<path id="Background" d="M452.9,376.3a26.1,26.1,0,0,1-25.5,20.8H162.6a26.1,26.1,0,0,1-26-26V193.2a26.1,26.1,0,0,1,26-26H427.4a26.1,26.1,0,0,1,26,26V371.1a25.9,25.9,0,0,1-.5,5.2" fill="#d960ae" stroke="#543093" stroke-miterlimit="10" stroke-width="5"/>
					<g id="paper-group">
						<rect id="paper" x="157.3" y="87.6" width="275.3" height="266.33" rx="26" ry="26" fill="#fff" stroke="#543093" stroke-miterlimit="10" stroke-width="5"/>
						<g id="face">
							<g id="mouth">
								<path id="mouth-scared" d="M275,220a18.7,18.7,0,0,1,35.9.1" fill="none" stroke="#543093" stroke-linecap="round" stroke-miterlimit="10" stroke-width="5"/>
								<path id="mouth-sad" d="M258.8,231.9c3.9-14.5,17.7-25.2,34-25.2s30.3,10.8,34.1,25.4" fill="none" stroke="#543093" stroke-linecap="round" stroke-miterlimit="10" stroke-width="5"/>
								<path id="mouth-worry" d="M271.1,218.7c10-11.1,28.2-15,43.6-9.4" fill="none" stroke="#543093" stroke-linecap="round" stroke-miterlimit="10" stroke-width="5"/>
								<path id="mouth-happy" d="M259.3,207c3.9,14.5,17.7,25.2,34,25.2s30.3-10.8,34.1-25.4" fill="none" stroke="#543093" stroke-linecap="round" stroke-miterlimit="10" stroke-width="5"/>
								<g id="mouth-laughing">
									<path id="open-mouth" d="M323.8,208.3c3.9,0,6.7,3.9,5.9,7.9a37.5,37.5,0,0,1-73.5,0c-0.9-4.1,2-7.9,5.9-7.9h61.7Z" fill="#543093" opacity="0.98"/>
									<path id="tongue" d="M293.2,241.1c6.9,0,13.1-2.3,17.3-5.9a2.1,2.1,0,0,0,.5-2.6c-3.1-5.8-9.9-9.8-17.8-9.8s-14.7,4-17.8,9.8a2.1,2.1,0,0,0,.5,2.5C280,238.8,286.2,241.1,293.2,241.1Z" fill="#d960ae"/>
								</g>
							</g>
							<g id="eye-group">
								<g id="eyes" class="eyes">
									<ellipse id="eye-right" cx="349" cy="172.8" rx="11.2" ry="13.8" fill="#543093" stroke-linecap="round" stroke-miterlimit="10" stroke-width="5" />
									<ellipse id="eye-left" cx="235.5" cy="172.8" rx="11.2" ry="13.8" fill="#543093" stroke-linecap="round" stroke-miterlimit="10" stroke-width="5" />         <path id="eyebrow-sad-right" d="M341.9,133.7c2.6,5.3,14.8,14.1,24.3,14.7" fill="none" stroke="#543093" stroke-linecap="round" stroke-miterlimit="10" stroke-width="5"/>
									<path id="eyebrow-sad-left" d="M240.7,133.7c-2.6,5.3-14.8,14.1-24.3,14.7" fill="none" stroke="#543093" stroke-linecap="round" stroke-miterlimit="10" stroke-width="5"/>

								</g>
								<g id="eyes-laughing">
									<path id="eye-laughing-right" d="M332.2,174c0-8.3,7.5-15,16.8-15s16.8,6.7,16.8,15" fill="none" stroke="#543093" stroke-linecap="round" stroke-miterlimit="10" stroke-width="5"/>
									<path id="eye-laughing-left" d="M218.7,174c0-8.3,7.5-15,16.8-15s16.8,6.7,16.8,15" fill="none" stroke="#543093" stroke-linecap="round" stroke-miterlimit="10" stroke-width="5"/>
								</g>
								<g id="eyebrows-happy">
									<path id="eyebrow-happy-right" d="M366.2,146.3c-2.6-5.3-14.8-14.1-24.3-14.7" fill="none" stroke="#543093" stroke-linecap="round" stroke-miterlimit="10" stroke-width="5"/>
									<path id="eyebrow-happy-left" d="M216.4,146.3c2.6-5.3,14.8-14.1,24.3-14.7" fill="none" stroke="#543093" stroke-linecap="round" stroke-miterlimit="10" stroke-width="5"/>
								</g>
							</g>
						</g>
					</g>
					<path id="back" d="M451.9,186.7S322.4,288.2,313.4,294.1s-27,5.8-36.9,0S137.9,186.5,137.9,186.5a23.6,23.6,0,0,0-1.3,6.7V371.1a26.1,26.1,0,0,0,26,26H427.4a26,26,0,0,0,26-26V193.2C453.4,190.7,452.5,188.9,451.9,186.7Z" fill="#f3c1df" stroke="#543093" stroke-miterlimit="10" stroke-width="5"/>
					<g id="shadow">
						<path id="shadow-3" d="M263.3,279.7s11.3-8.1,13.1-9.3c9.9-6.5,27-5.8,36.9,0,1.7,1,13.5,9.3,13.5,9.3" fill="none" stroke="#eddfeb" stroke-linejoin="bevel" stroke-width="7"/>
						<path id="shadow-2" d="M430.2,193.3L313.4,282.2a26.1,26.1,0,0,1-36.8,0L159.8,193.3V201l116.8,90.6c7.9,5.7,26.9,6.4,37,0l116.6-90.9v-7.4Z" fill="#eddfeb"/>
					</g>
					<path id="shadow-1" d="M425.2,381.5h-262c-14.1,0-24.2-11-24.2-24.4v13.2c0,13.4,10.1,24.3,24.2,24.3h262c12.7,1.2,23.9-8.4,25.2-19.5a42.8,42.8,0,0,0,.5-4.9V358.1a14.7,14.7,0,0,1-.5,3.9C448,373.1,437.6,381.5,425.2,381.5Z" fill="#d960ae" opacity="0.5"/>
					<g id="Front">
						<path id="Front-2" data-name="Front" d="M139.8,381.9s127.5-99.5,136.5-105.4,27-5.8,36.9,0S449.8,382.1,449.8,382.1" fill="none" stroke="#543093" stroke-miterlimit="10" stroke-width="5"/>
						<path id="Front-3" data-name="Front" d="M225.4,315.3s41.9-33,51-38.9,27-5.8,36.9,0S355,307.9,355,307.9" fill="#f3c1df" stroke="#543093" stroke-miterlimit="10" stroke-width="5"/>
					</g>
				</g>
			</svg>
			<div class="bottom">
				<h4 class="title"></h4>
				<p class="subtitle"><p/>
					<input type="hidden" id="contactid" value="<?php echo $contact_id; ?>">
					<input type="hidden" id="email" value="<?php echo $email_address; ?>">
					<input type="hidden" id="contact_type" value="<?php echo $type; ?>">
					<input type="hidden" id="cam_id" value="<?php echo $cam_id; ?>">
					<input type="hidden" id="campaign_type" value="<?php echo $campaign_type; ?>">

					<input type="hidden" id="cam_temp_table_id" value="<?php echo $cam_temp_table_id; ?>">
					<div class="buttons">
						<button class="btn btn-primary" id="subscribed" button-type="sub"><i class="fa fa-bell"></i> <?php echo $this->lang->line("Subscribe");?></button>
						<button class="btn btn-danger" id="unsubscribe" button-type="unsub"><i class="fa fa-bell-slash"></i> <?php echo $this->lang->line("Unsubscribe");?></button>
					</div>
				</div>
		</div>

	</div>

	<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<script src="https://use.fontawesome.com/24f7885cb9.js"></script>
	<script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/16327/MorphSVGPlugin.min.js"></script>
	<script src="<?php echo base_url('plugins/alertifyjs/alertify.min.js')?>"></script>
	<script src="<?php echo base_url(); ?>assets/modules/izitoast/js/iziToast.min.js"></script>


	<script>
		$(document).ready(function($) {

			var base_url = '<?php echo base_url() ?>';
			var status = '<?php echo $status; ?>';

			if(status == '0') {
				$(".bottom .title").html('<?php echo $this->lang->line('Do you want to unsubscribe?'); ?>');
				$(".bottom .subtitle").html('<?php echo $this->lang->line('If you unsubscribe, you will stop receiving our emails.'); ?>');
				$("#unsubscribe").css("display","block");
				$("#subscribed").css("display","none");
			}

			if(status == '1') {
				$(".bottom .title").html('<?php echo $this->lang->line('Do you want to Subscribe?'); ?>');
				$(".bottom .subtitle").html('<?php echo $this->lang->line('If you subscribe, you will start receiving our emails.'); ?>');
				$("#unsubscribe").css("display","none");
				$("#subscribed").css("display","block");
			}

			$(document).on('click', '#unsubscribe', function(event) {
				event.preventDefault();
				var contactid = $("#contactid").val();
				var email 	  = $("#email").val();
				var cam_id 	  = $("#cam_id").val();
				var cam_temp_table_id = $("#cam_temp_table_id").val();
				var btntype   = $(this).attr("button-type");
				var contactType = $("#contact_type").val();
				var campaign_type = $("#campaign_type").val();

				$(this).addClass('btn-progress');
				var that = $(this);

				if(contactid != '' || email != '')
				{
					$.ajax({
						url: base_url+'home/unsubscribe_action',
						type: 'POST',
						data: {contactid: contactid, email:email, btntype:btntype, contactType:contactType,cam_id:cam_id,cam_temp_table_id:cam_temp_table_id,campaign_type : campaign_type},
						success:function(response) {
							$(that).removeClass('btn-progress');
							if(response == "1")
							{

								iziToast.success({title: '',message: '<?php echo $this->lang->line("you have successfully unsubscribed from our mailing services.")?>',position: 'bottomRight'});
								$("#subscribed").show();
								$("#unsubscribe").hide();
								$(".bottom .title").html('<?php echo $this->lang->line('Do you want to Subscribe?'); ?>');
								$(".bottom .subtitle").html('<?php echo $this->lang->line('If you subscribe, you will start receiving our emails.'); ?>');

							} else if(response == "0") {
								iziToast.success({title: '',message: '<?php echo $this->lang->line("Something went wrong, please try once again.")?>',position: 'bottomRight'});

							}

						}
					})
				}
			});

			$(document).on('click', '#subscribed', function(event) {
				event.preventDefault();

				var contactid = $("#contactid").val();
				var email 	  = $("#email").val();
				var btntype   = $(this).attr("button-type");
				var contactType = $("#contact_type").val();
				var cam_id 	  = $("#cam_id").val();
				var cam_temp_table_id = $("#cam_temp_table_id").val();
				var campaign_type = $("#campaign_type").val();

				$(this).addClass('btn-progress');
				var that = $(this);

				if(contactid != '' || email != '')
				{
					$.ajax({
						url: base_url+'home/unsubscribe_action',
						type: 'POST',
						data: {contactid: contactid, email:email, btntype:btntype, contactType:contactType,cam_id:cam_id,cam_temp_table_id:cam_temp_table_id,campaign_type:campaign_type},
						success:function(response)
						{
							$(that).removeClass('btn-progress');
							if(response == "1")
							{
								iziToast.success({title: '',message: '<?php echo $this->lang->line("you have successfully subscribed into our mailing services.")?>',position: 'bottomRight'});
								$("#subscribed").hide();
								$("#unsubscribe").show();
								$(".bottom .title").html('<?php echo $this->lang->line('Do you want to unsubscribe?'); ?>');
								$(".bottom .subtitle").html('<?php echo $this->lang->line('If you unsubscribe, you will stop receiving our emails.'); ?>');
								
							} else if(response == "0") {
								iziToast.success({title: '',message: '<?php echo $this->lang->line("Something went wrong, please try once again.")?>',position: 'bottomRight'});
							}

						}
					})
				}
			});
		});

	</script>


	<script>
		
		var windowWidth = window.innerWidth;
		var windowHeight = window.innerHeight;

		function setWindowSize() {
		    windowWidth = window.innerWidth;
		    windowHeight = window.innerHeight;
		};
		window.addEventListener('resize', setWindowSize);

		var eyes = document.querySelectorAll(".eyes");
		var cursorPos = { x: 0, y: 0 };

		window.addEventListener("mousemove", mousemove);
		window.addEventListener("touchmove", touchmove);

		function mousemove(e) {
		  cursorPos = {
		    x: e.clientX,
		    y: e.clientY
		  }; 
			if (!clicked) {
			  eyes.forEach(function(el) {
				  eyeFollow.init(el);
			  })
			}
		}
		function touchmove(e) {
		  cursorPos = {
		    x: e.targetTouches[0].offsetX,
		    y: e.targetTouches[0].offsetY
		  }; 
			if (!clicked) {
			  eyes.forEach(function(el) {
				  eyeFollow.init(el);
			  })
			}
		}

		var eyeFollow = (function() {

			function getOffset(el) {
		  		el = el.getBoundingClientRect();
				return {
					x: el.left + window.scrollX,
					y: el.top + window.scrollY
				};
			}
			
			function moveEye(eye) {
				var eyeOffset = getOffset(eye);
				var bBox = eye.getBBox();
				var centerX = eyeOffset.x + bBox.width / 2;
				var centerY = eyeOffset.y + bBox.height / 2;
				var percentTop = Math.round((cursorPos.y - centerY) * 100 / windowHeight);
				var percentLeft = Math.round((cursorPos.x - centerX) * 100 / windowWidth);
				eye.style.transform = `translate(${percentLeft/5}px, ${ percentTop/5}px)`
			}
			
			return {
		    init: (el) => {
		      moveEye(el);
		    }
		  };
		})();



		var clicked, cancelled;
		var animate = (function() {

			var select = function(el) {
				 return document.getElementById(el);
			};
			var svg = select("svg"),
				 blob1 = select("blob-1"),
				 blob3 = select("blob-3"),
				 envelope = select("envelope"),
				 eyeGroup = select("eye-group"),
				 paper = select("paper-group"),
				 mouth = select("mouth"),
				 mouthHappy = select("mouth-happy"),
				 mouthScared = select("mouth-scared"),
				 mouthSad = select("mouth-sad"),
				 eyeLeft = MorphSVGPlugin.convertToPath(select("eye-left")),
				 eyeRight = MorphSVGPlugin.convertToPath(select("eye-right")),
				 eyeLaughingLeft = select("eye-laughing-left"),
				 eyeLaughingRight = select("eye-laughing-right"),
				 eyebrowHappyLeft = select("eyebrow-happy-left"),
				 eyebrowHappyRight = select("eyebrow-happy-right"),
				 eyebrowSadLeft = select("eyebrow-sad-left"),
				 eyebrowSadRight = select("eyebrow-sad-right"),
				 mouthWorry = select("mouth-worry"),
				 openMouth = select("open-mouth"),
				 tongue = select("tongue"),
				 unsubscribeButton = select("unsubscribe"),
				 cancelButton = select("cancel"),
				 goBackButton = select("go-back");

			function animateBlob() {
				var speed = 10;
				var tlBlob = new TimelineMax({repeat:-1});
				tlBlob.to(blob1, speed, {morphSVG:blob3, ease: Power0.easeNone})
						.to(blob1, speed, {morphSVG:blob1, ease: Power0.easeNone});
			}


			//Envelope animations
			function happyJump() {
				var speed = 0.15;
				var happyJumpTl = new TimelineMax({repeat:-1, repeatDelay: 1, delay:0.5, paused:true});
				happyJumpTl.to(envelope, speed, {y:-20, ease: Power0.easeNone})
				.to(envelope, speed, {y:0, ease: Power0.easeNone})
				.to(envelope, speed, {y:-10, ease: Power0.easeNone})
				.to(envelope, speed, {y:0, ease: Power0.easeNone})
				.to(envelope, speed, {y:-5, ease: Power0.easeNone})
				.to(envelope, speed, {y:0, ease: Power0.easeNone});
				return happyJumpTl;
			}

			 function shake() {
				var speed = 0.1;
				var shakeTl = new TimelineMax({repeat:-1, paused:true});
				shakeTl.to(envelope, speed, {x:-1, ease: Power0.easeNone})
				.to(envelope, speed, {x:1, ease: Power0.easeNone});
				return shakeTl;
			 }

			var doJump = happyJump();
			var doShake = shake();

		})();


		function random(min, max) {
		  if (max == null) {
		    max = min;
		    min = 0;
		  }
		  return Math.random() * (max - min) + min;
		}

	</script>
</body>






