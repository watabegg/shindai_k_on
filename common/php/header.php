<?php
    $url = 'http://' . $_SERVER['HTTP_HOST'] . '/shindai_k_on/index.html';
    $cssurl = 'http://' . $_SERVER['HTTP_HOST'] . '/shindai_k_on/common/css/menu.css';
    $jsurl = 'http://' . $_SERVER['HTTP_HOST'] . '/shindai_k_on/common/js/menu.js';

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
                            <p>鋭意製作中</p>
                        </li>
                        <li>
                            <p>きっと来ない</p>
                        </li>
                        <li>
                            <p>本当の終わりはやってこない</p>
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