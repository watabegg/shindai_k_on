<?php
    $url = 'http://localhost/shindai_k_on/index.html';
    $cssurl = 'http://localhost/shindai_k_on/common/css/menu.css';
    $jsurl = 'http://localhost/shindai_k_on/common/js/menu.js';

    echo <<<_HTML_
    <link rel="stylesheet" href="$cssurl">
    <script src="$jsurl"></script>
    <a href="$url">
        <h1 class="head">信州大学軽音楽部</h1>
    </a>
        <div id="navArea">
            <nav>
                <div class="inner">
                    <ul>
                        <li class="option">
                            <p>まだ出来てねンだわ</p>
                        </li>
                        <li>
                            <p>もうちょい待っててな</p>
                        </li>
                    </ul>
                </div>
            </nav>
            <div class="toggle-btn-circle">
                <div class="toggle_btn">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
            <div class="mask"></div>
        </div>
    _HTML_;
?>