<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USG-Officer_Dashboard</title>
    <link rel="icon" href="../img/USG-Logo.jpg"/>
    
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="../node_modules/bootstrap-icons/font/bootstrap-icons.min.css">
    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<style>

@import url('https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

*{
    font-family: "Poppins", sans-serif;
    font-weight: 400;
    font-style: normal;
}
.wrapper{
    display: flex;
    
}
a{
    text-decoration: none;
}
li{
    list-style: none;
}
.main{
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    width: 100%;
    overflow: hidden;
    transition: all 0.25s ease-in-out;
    background-color: #fff;
}
#sidebar{
    width: 90px;
    min-width: 90px;
    transition: all 0.25s ease-in-out;
    background: linear-gradient(135deg, #232526 0%, #211948 100%);
    display: flex;
    flex-direction: column;
}
#sidebar.expand{
    width: 260px;
    min-width: 260px;
}
#sidebar:not(.expand) .sidebar-logo{
    display: none;
}
.toggle-btn{
    width: 30px;
    height: 40px;
    color: #fff;
    border-radius: 0.425rem;
    font-size: 18px;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #323c55;
}
.toggle-btn i{
    color: #fff;
}
#sidebar.expand.sidebar-logo{
    animation: fadeln.25s ease;
}
@keyframes fadeln{
    0%{
        opacity: 0;
    }
    100%{
        opacity: 1;
    }
}
.sidebar-logo a{
    color: #fff;
    font-size: 1.15rem;
    font-weight: 600;
}

</style>

</head>
<body>
    
    <div class="wrapper">
        <aside id="sidebar">

            <div class="d-flex justify-content-between p-4">
                <div class="sidebar-logo">
                    <a href="#">USG</a>
                </div>
                <button class="toggle-btn border-0" type="button">
                    <i id="icon" class="bi bi-chevron-double-right"></i>
                </button>
            </div>

            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-list-task"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-people"></i>
                        <span>Attendance</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-plus-circle"></i>
                        <span>Add Event</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-cash-coin"></i>
                        <span>Payments</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-question-diamond"></i>
                        <span>Lost and Found</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                        <span>Generate Report</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Log Out</span>
                    </a>
                </li>
            </ul>

        </aside>
        <div class="main"></main>
        </div>
    </div>

    <script src="../src//js-functions/off-dashboard.js"></script>

</body>
</html>