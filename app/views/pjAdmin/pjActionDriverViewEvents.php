
   <html>
        <head>
            <title>${name}</title>
            <style>
                html, body {
                    margin: 0;
                    padding: 40px;
                    width: 100%;
                    height: 100%;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    background: #fff;
                    overflow: hidden;
                }

                .guest-box {
                    border: 6px solid #e67e22;
                    width: 30%;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }

                .guest-text {
                    font-size: 100px;
                    font-weight: bold;
                    color: #e67e22;
                    writing-mode: vertical-rl;
                    text-orientation: mixed;
                    white-space: nowrap;
                }
            </style>
        </head>

        <body>
            <div class="guest-box">
                <div class="guest-text"><?php echo $tpl['bookedName'] ?></div>
            </div>
        </body>
        </html>