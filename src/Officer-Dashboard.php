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
    transition: all 0.4s ease-in-out;
    background: linear-gradient(135deg, #232526 0%, #211948 100%);
    display: flex;
    flex-direction: column;
}
#sidebar.expand{
    width: 280px;
    min-width: 280px;
}
#sidebar:not(.expand) .sidebar-logo,
#sidebar:not(.expand) a.sidebar-link span{
    display: none;
}
.toggle-btn{
    width: 40px;
    height: 40px;
    color: #fff;
    border-radius: 0.425rem;
    font-size: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #323c55;
}
.toggle-btn i{
    color: #fff;
}
#sidebar.expand .sidebar-logo,
#sidebar.expand a.sidebar-link span{
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
    font-size: 1.3rem;
    font-weight: 600;
}
.sidebar-nav{
    padding: 0.7rem 0;
    flex: 11 auto;
    z-index: 10;
}
a.sidebar-link{
    padding: .625rem 1.625rem;
    color: #fff;
    display: block;
    white-space: nowrap;
    font-weight: 700;
    border-left: 3px solid transparent;
}
.sidebar-link i{
    font-size: 1.1rem;
    margin-right: .75rem;
}
a.sidebar-link:hover{
    background-color: rgba(255, 255, 255, 0.075);
    border-left: 6px solid #f69526;
}
.sidebar-item{
    position:relative;
}


</style>

</head>
<body>
    
    <!-- SIDEBAR -->

    <div class="wrapper">
        <aside id="sidebar">

            <div class="d-flex justify-content-between align-items-center p-4">
                <div class="sidebar-logo">
                    <a href="#">UNIVERSITY OF STUDENT GOVERNMENT</a>
                </div>
                <button class="toggle-btn border-0" type="button">
                    <i id="icon" class="bi bi-list"></i>
                </button>
            </div>


    <!-- ICONS -->

            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-house"></i>
                        <span>HOME</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-people"></i>
                        <span>ATTENDANCE</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-plus-circle"></i>
                        <span>ADD EVENT</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-cash-coin"></i>
                        <span>PAYMENTS</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-question-diamond"></i>
                        <span>LOST AND FOUND</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                        <span>GENERATE REPORTS</span>
                    </a>
                </li>
            </ul>

    <!-- LOGOUT -->

            <div class="sidebar-footer">
                <a href="#" class="sidebar-link">
                    <i class="bi bi-box-arrow-left"></i>
                    <span>LOG OUT</span>
                </a>
            </div>

        </aside>

    <!-- MAIN -->

        <div class="main">
        </div>

    </div>

    <script src="../src/js-functions/off-dashboard.js"></script>

</body>
</html>