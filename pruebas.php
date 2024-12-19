<?php 
    include ("header.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .wrapper {
            position: fixed; /* Asegura que la posición sea fija en la ventana */
            bottom: 20px; /* Espaciado desde la parte inferior */
            right: 20px; /* Espaciado desde la parte derecha */
            list-style: none;
            height: 120px;
            width: auto;
            padding-top: 40px;
            font-family: "Poppins", sans-serif;
            justify-content: center;
            z-index: 1000; /* Asegura que esté por encima de otros elementos */
        }

        .wrapper .icon {
            position: relative;
            background: #fff;
            border-radius: 50%;
            margin: 10px;
            width: 50px;
            height: 50px;
            font-size: 18px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            box-shadow: 0 10px 10px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .wrapper .tooltip {
            position: absolute;
            top: 0;
            font-size: 14px;
            background: #fff;
            color: #fff;
            padding: 5px 8px;
            border-radius: 5px;
            box-shadow: 0 10px 10px rgba(0, 0, 0, 0.1);
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .wrapper .tooltip::before {
            position: absolute;
            content: "";
            height: 8px;
            width: 8px;
            background: #fff;
            bottom: -3px;
            left: 50%;
            transform: translate(-50%) rotate(45deg);
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .wrapper .icon:hover .tooltip {
            top: -45px;
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .wrapper .icon:hover span,
        .wrapper .icon:hover .tooltip {
            text-shadow: 0px -1px 0px rgba(0, 0, 0, 0.1);
        }

        .wrapper .whatsapp:hover,
        .wrapper .whatsapp:hover .tooltip,
        .wrapper .whatsapp:hover .tooltip::before {
            background: #25d366;
            color: #fff;
        }
    </style>
    <!-- Asegúrate de incluir la librería de FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <ul class="wrapper">
        <li class="icon whatsapp">
            <span class="tooltip">whatsapp</span>
            <i class="fab fa-whatsapp"></i>
        </li>
    </ul>
</body>

</html>
