<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard-2</title>
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
li{
    list-style: none;
}
a{
    text-decoration: none;
}
.main{
    min-height: 100vh;
    width: 100%;
    overflow: hidden;
    background-color: #fff;
}
#sidebar{
    max-width: 246px;
    min-width: 246px;
    transition: all 0.4s ease-in-out;
    display: flex;
    flex-direction: column;
}
#sidebar.collapsed{
    margin-left: -246px;
}
.toggler-btn{
    background-color: transparent;
    cursor: pointer;
    border: 0;
}
.toggler-btn i{
    font-size: 1.5rem;
    color: #000;
    font-weight: bold;
}
.navbar{
    padding: 1.15rem 1.5rem;
}
.sidebar-nav{
    flex: 1 1 auto;
}
.sidebar-logo{
    padding: 1.5rem 1.5rem;
    text-align: center;
}
.sidebar-logo a{
    font-size: 1.5rem;
    color: #fff;
    font-weight: 800;
}
a.sidebar-link{
    padding: .625rem 1.625rem;
    color: #fff;
    position: relative;
    transition: all 0.3s ease-in-out;
    display: block
}
@media (max-width: 768px){
    .sidebar-toggle{
        margin-left: -246px;
    }
    #sidebar.collapsed{
        margin-left: 0;
    }
}

</style>

</head>
<body>
    
    <div class="d-flex">
    
    <!-- Sidebar -->
        <aside id="sidebar">
            <div class="sidebar-logo">
                <a href="#">Dashboard</a>
            </div>

    <!-- Sidebar Navigation-->
            <ul class="sidebar-nav p-0">
                <li class="sidebar-header">
                    SERVICES
                </li>
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

            <div class="sidebar-footer">
                <a href="#" class="sidebar-link">
                    <i id="icon" class="bi bi-box-arrow-left"></i>
                    <span>LOG OUT</span>
                </a>
            </div>
        </aside>

    <!-- Main Component -->
        <div class="main">
            <nav class="navbar navbar-expand border-bottom">
                <button class="toggler-btn" type="button">
                    <i class="lni lni-text-align-left"></i>
                </button>
            </nav>
            <main class="p-3">
                <div class="container-fluid">
                    <div class="mb-3 text-center">
                        <h1>UNIVERSITY OF STUDENT GOVERNMENT</h1>
                    </div>
                </div>
            </main>
        </div>

    </div>

    <script src="../src/js-functions/off-dashboard-2.js"></script>

</body>
</html>