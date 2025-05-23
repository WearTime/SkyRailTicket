<?php
$currentPath = $_SERVER['REQUEST_URI'];
?>


<head>
    <link rel="stylesheet" href="../assets/css/sidebarAdmin.css">
</head>

<sidebar class="sidebar-container">
    <ul>
        <li class="<?= $currentPath == '/skyrailticket/admin/' ? 'active' : '' ?>">
            <a href="/skyrailticket/admin/">
                <svg height="20px" fill="<?= $currentPath == '/skyrailticket/admin/' ? '#000000' : '#ffffff' ?>"
                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
                    id="dashboard" class="icon glyph">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                        <rect x="2" y="2" width="9" height="11" rx="2"></rect>
                        <rect x="13" y="2" width="9" height="7" rx="2"></rect>
                        <rect x="2" y="15" width="9" height="7" rx="2"></rect>
                        <rect x="13" y="11" width="9" height="11" rx="2"></rect>
                    </g>
                </svg>
            </a>
        </li>
        <li class="<?= $currentPath == '/skyrailticket/admin/ticket' ? 'active' : '' ?>">
            <a href="/skyrailticket/admin/ticket">
                <svg fill="<?= $currentPath == '/skyrailticket/admin/ticket' ? '#000000' : '#ffffff' ?>"
                    xmlns="http://www.w3.org/2000/svg" height="20px" width="20px" version="1.1" id="Capa_1"
                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 297 297"
                    xml:space="preserve">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                        <g>
                            <path
                                d="M224.147,116.398c-0.49-0.49-1.168-0.77-1.86-0.77h-10.164c1.757,1.477,2.463,3.877,1.732,6.076 c-0.765,2.302-2.919,3.857-5.346,3.857h-52.582c-2.171,0-4.15-1.248-5.084-3.207l-3.208-6.725h-24.67 c-2.584,0-4.838-1.759-5.465-4.267l-4.24-16.961c-3.372,6.33-8.64,18.23-8.039,29.912c0.005,0.096,0.007,0.193,0.007,0.289 c0,4.467,7.291,8.973,23.579,8.973h106.166c1.45,0,2.629-1.179,2.629-2.629c0-0.702-0.274-1.363-0.77-1.86L224.147,116.398z">
                            </path>
                            <polygon points="191.558,114.294 143.099,78.092 142.213,78.092 159.481,114.294 "></polygon>
                            <path
                                d="M286.652,118.943h-7.31v48.767c0,13.612-11.074,24.685-24.686,24.685H36.726v48.767c0,5.706,4.643,10.348,10.348,10.348 h239.577c5.706,0,10.348-4.643,10.348-10.348V129.29C297,123.585,292.357,118.943,286.652,118.943z">
                            </path>
                            <path
                                d="M265.004,167.71V55.839c0-5.706-4.643-10.348-10.348-10.348H10.348C4.643,45.49,0,50.133,0,55.839V167.71 c0,5.706,4.643,10.347,10.348,10.347h244.307C260.361,178.057,265.004,173.415,265.004,167.71z M77.969,135.769H22.88 c-3.11,0-5.633-2.522-5.633-5.633c0-3.11,2.522-5.633,5.633-5.633h55.089c3.11,0,5.633,2.522,5.633,5.633 C83.602,133.247,81.08,135.769,77.969,135.769z M77.969,117.407H41.243c-3.11,0-5.633-2.522-5.633-5.633s2.522-5.633,5.633-5.633 h36.727c3.11,0,5.633,2.522,5.633,5.633S81.08,117.407,77.969,117.407z M234.975,144.84H128.808 c-31.978,0-34.787-15.154-34.844-20.08c-1.006-20.71,12.083-40.444,12.641-41.277c1.046-1.557,2.799-2.491,4.676-2.491h5.843 c2.584,0,4.838,1.759,5.465,4.267l4.775,19.104h14.899l-14.06-29.478c-0.833-1.745-0.712-3.796,0.32-5.431s2.83-2.627,4.764-2.627 h11.685c1.215,0,2.397,0.393,3.371,1.12l48.744,36.416h25.201c3.714,0,7.203,1.446,9.827,4.072l12.685,12.686 c2.625,2.624,4.071,6.114,4.071,9.825C248.87,138.607,242.637,144.84,234.975,144.84z">
                            </path>
                        </g>
                    </g>
                </svg>
            </a>
        </li>
        <li class="<?= $currentPath == '/skyrailticket/admin/booking' ? 'active' : '' ?>">
            <a href="">
                <svg fill="<?= $currentPath == '/skyrailticket/admin/booking' ? '#000000' : '#ffffff' ?>" height="24px"
                    version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 491.526 491.526" xml:space="preserve">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                        <g>
                            <g>
                                <g>
                                    <path
                                        d="M143.36,102.403c-11.305,0-20.48,9.195-20.48,20.48s9.175,20.48,20.48,20.48c11.305,0,20.48-9.196,20.48-20.48 S154.665,102.403,143.36,102.403z">
                                    </path>
                                    <path
                                        d="M491.52,20.483c0-11.305-9.155-20.48-20.48-20.48H20.48C9.155,0.003,0,9.178,0,20.483v266.24h491.52V20.483z M143.36,184.323c-33.874,0-61.44-27.566-61.44-61.44s27.566-61.44,61.44-61.44c33.874,0,61.44,27.566,61.44,61.44 S177.234,184.323,143.36,184.323z M424.079,96.402l-34.959,34.959v73.441c0,11.305-9.155,20.48-20.48,20.48 s-20.48-9.175-20.48-20.48v-32.481l-67.441,67.441c-3.994,3.994-9.236,6.001-14.479,6.001c-5.243,0-10.486-2.007-14.479-6.001 l-40.96-40.96c-8.008-8.008-8.008-20.951,0-28.959s20.951-8.008,28.959,0l26.481,26.481l52.961-52.961H286.72 c-11.326,0-20.48-9.175-20.48-20.48c0-11.305,9.155-20.48,20.48-20.48h73.441l34.959-34.959c8.008-8.008,20.951-8.008,28.959,0 C432.087,75.451,432.087,88.395,424.079,96.402z">
                                    </path>
                                    <path
                                        d="M0.006,327.683v61.44c0,11.305,9.155,20.48,20.48,20.48h152.453c-1.823,13.107-7.004,27.443-11.796,31.007 l-13.844,10.281c-7.885,5.734-11.162,15.872-8.11,25.19c2.99,9.236,11.469,15.442,21.115,15.442h170.762 c9.626,0,18.104-6.185,21.115-15.38c3.031-9.318-0.205-19.436-7.946-25.108l-13.988-10.424 c-4.772-3.523-9.912-17.879-11.694-31.007h152.494c11.305,0,20.48-9.175,20.48-20.48v-61.44H0.006z M203.229,450.563 c6.042-12.431,9.81-27.197,10.977-40.96h63.099c1.126,13.763,4.895,28.549,10.895,40.96H203.229z">
                                    </path>
                                </g>
                            </g>
                        </g>
                    </g>
                </svg>
            </a>
        </li>
        <li class="<?= $currentPath == '/admin/user' ? 'active' : '' ?>">
            <a href="/userx">
                <svg height="29px" viewBox="0 0 24 24"
                    fill="<?= $currentPath == '/admin/user' ? '#000000' : '#ffffff' ?>"
                    xmlns="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M10 4H14C17.7712 4 19.6569 4 20.8284 5.17157C22 6.34315 22 8.22876 22 12C22 15.7712 22 17.6569 20.8284 18.8284C19.6569 20 17.7712 20 14 20H10C6.22876 20 4.34315 20 3.17157 18.8284C2 17.6569 2 15.7712 2 12C2 8.22876 2 6.34315 3.17157 5.17157C4.34315 4 6.22876 4 10 4ZM13.25 9C13.25 8.58579 13.5858 8.25 14 8.25H19C19.4142 8.25 19.75 8.58579 19.75 9C19.75 9.41421 19.4142 9.75 19 9.75H14C13.5858 9.75 13.25 9.41421 13.25 9ZM14.25 12C14.25 11.5858 14.5858 11.25 15 11.25H19C19.4142 11.25 19.75 11.5858 19.75 12C19.75 12.4142 19.4142 12.75 19 12.75H15C14.5858 12.75 14.25 12.4142 14.25 12ZM15.25 15C15.25 14.5858 15.5858 14.25 16 14.25H19C19.4142 14.25 19.75 14.5858 19.75 15C19.75 15.4142 19.4142 15.75 19 15.75H16C15.5858 15.75 15.25 15.4142 15.25 15ZM11 9C11 10.1046 10.1046 11 9 11C7.89543 11 7 10.1046 7 9C7 7.89543 7.89543 7 9 7C10.1046 7 11 7.89543 11 9ZM9 17C13 17 13 16.1046 13 15C13 13.8954 11.2091 13 9 13C6.79086 13 5 13.8954 5 15C5 16.1046 5 17 9 17Z"
                            fill="#fff"></path>
                    </g>
                </svg>
            </a>
        </li>
        <li></li>
    </ul>
</sidebar>