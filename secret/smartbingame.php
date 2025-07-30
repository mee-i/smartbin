<?php
session_start();
date_default_timezone_set("Asia/Jakarta");

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    if ($_SESSION['gameticket'] != $_GET['ticket']) {
        echo "Tiket game anda tidak valid, coba klik kembali logo 'SmartBin' di <a href='/'>homepage</a>.";
        exit;
    }
}
file_put_contents("accessed.log", "Accessed at ".date("d/m/Y H:i:s")."\n", FILE_APPEND);
?>

<html>
  <head>
    <title>SmartBin Game</title>
    <meta name="viewport" content="width=device-width, user-scalable=">
    <style>
      @media screen and (orientation:portrait) {
        body {
          width: 100vh;
          height: 100vw;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%) rotate(90deg);
        }
      }
      * {
        user-select: none;
      }
      body {
        padding: 0;
        margin: 0;
        position: fixed;
        font-family: Arial;
      }
      canvas {
        width: 100%;
        z-index: 1;
        position: fixed;
      }
      .button {
        border: none;
        border-radius: 5px;
        font-size: 25px;
        height: 50px;
        display: inline-block;
        position: fixed;
        left: 50%;
        margin-top: 30px;
        transform: translate(-50%, -50%);
        background-color: #909090;
      }
      .button-icon {
        position: relative;
        top: 2px;
        height: 25px;
        padding-left: 5px;
        padding-right: 5px;
        background-color: #909090;
      }
      .score-box {
        position: absolute;
        top: 5px;
        left: 5px;
        padding: 5px;
        background-color: rgba(255,255,255,0.5);
        font-weight: bold;
        z-index: 99;
      }
      #game-over, #game-start {
        z-index: 2;
        position: fixed;
        top: 36%;
        left: 50%;
        padding: 10px;
        transform: translate(-50%, -50%);
        align-items: center;
        background-color: rgba(255,255,255,0.5);
      }
    </style>
  </head>
  <body onload="Game.startGame()">
    <div class="score-box">
      <span id="score-display">SCORE: 0</span>
    </div>
    <div id="game-over" hidden>
      <h3>GAME OVER</h3>
      <button class="button start-button" onclick="Game.startGame()">
        <svg class="button-icon" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 400.0 400.0">
          <path fill="white" d="M90.00,20.00L120.00,60.00C160.00,40.00,240.00,40.00,290.00,70.00C340.00,100.00,360.00,150.00,360.00,200.00C360.00,300.00,300.00,360.00,200.00,360.00C100.00,360.00,40.00,300.00,40.00,200.00L80.00,200.00C80.00,280.00,120.00,320.00,200.00,320.00C280.00,320.00,320.00,280.00,320.00,200.00C320.00,120.00,250.00,60.00,140.00,90.00L180.00,140.00L70.00,130.00z"/>
        </svg>
      </button>
    </div>
    <div id="game-start">
      <h3>Touch screen to play game</h3>
    </div>
    <script>
      var Canvas = {
        elem: null,
        ctx: null,
        clear: null,
        scale: 1.2,
        update: function() {}
      };
      
      var Cube = {
        elem: null,
        speedX: 0,
        speedY: 0,
        accelerationX: 0,
        accelerationY: 0
      };
      
      var Game = {
        score: 0,
        duration: 0,
        startGame: true,
        gravity: 540,
        lastUpdate: new Date().getTime(),
        spawnObstacleTimeOut: 0,
        isRunning: false,
        start: function() {},
        update: function() {},
        spawnObstacle: function() {}
      };
      
      var Ground = {
        y: 270,
        color: "black",
        elem: null
      };
      
      var Obstacle = {
        elems: [],
        holes: []
      };
      
      var Debug = {
        showCollisionBox: false
      };
      
      var GameOverScreen = document.getElementById("game-over");
      var GameStartScreen = document.getElementById("game-start");
      var GameScoreDisplay = document.getElementById("score-display");
      
      Canvas.elem = document.createElement("canvas");
      Canvas.elem.width = 850 * Canvas.scale;
      Canvas.elem.height = 425 * Canvas.scale;
      document.body.appendChild(Canvas.elem);
      Canvas.ctx = Canvas.elem.getContext("2d");
      
      Canvas.clear = function clear() {
        Canvas.ctx.clearRect(0, 0, Canvas.elem.width, Canvas.elem.height);
      }
      
      function _checkCol(colbox1, coor1, colbox2, coor2) {
        var x1 = coor1.x + colbox1.x;
        var y1 = coor1.y + colbox1.y;
        var x2 = x1 + colbox1.width;
        var y2 = y1 + colbox1.height;
        
        var box2 = {
          x1: null,
          y1: null,
          x2: null,
          y2: null
        };
        box2.x1 = coor2.x + colbox2.x;
        box2.y1 = coor2.y + colbox2.y;
        box2.x2 = box2.x1 + colbox2.width;
        box2.y2 = box2.y1 + colbox2.height;
        
        var p = [box2.x1, box2.x2, box2.x1, box2.x2];
        var q = [box2.y1, box2.y1, box2.y2, box2.y2];
        
        for (var i = 0; i < 4; i++) {
          if (p[i] <= x2 && p[i] >= x1 && q[i] <= y2 && q[i] >= y1) {
            return true;
          }
        }
        return false;
      }
      function CheckCollision(ColBox1, Coor1, ColBox2, Coor2) {
        return (_checkCol(ColBox1, Coor1, ColBox2, Coor2) || _checkCol(ColBox2, Coor2, ColBox1, Coor1));
      }
      function CollisionBox(x, y, w, h) {
        this.x = x;
        this.y = y;
        this.width = w;
        this.height = h;
      }
      function Coor(x, y) {
        this.x = x;
        this.y = y;
      }
      function CanvasElement(width, height, type) {
        this.collisionBox = [];
        this.x = 0;
        this.y = 0;
        this.filter = "";
        this.type = type;
        if (this.type == "rect")
          this.color;
        else if (this.type == "img")
          this.img;
        if (this.type == "rect" || this.type == "img") {
          this.width = width;
          this.height = height;
        }
        else if (this.type == "arc") {
          this.radius = width;
          this.counterClockwise = height;
          this.startAngle;
          this.endAngle;
          this.color;
        }
        this.rotation;
        this.draw = function(e) {
          let x = this.x * Canvas.scale;
          let y = this.y * Canvas.scale;
          let width = this.width * Canvas.scale;
          let height = this.height * Canvas.scale;
          e.save();
          if (this.filter != "") {
            e.filter = this.filter;
          }
          if (this.rotation) {
            e.translate(x + (width / 2), y + (height / 2));
            e.rotate(this.rotation * Math.PI / 180);
            e.translate(-(x + (width / 2)), -(y + (height / 2)));
          }
          if (this.type == "rect") {
            e.fillStyle = this.color;
            e.beginPath();
            e.fillRect(x, y, width, height);
            e.fill();
          }
          else if (this.type == "img") {
            e.drawImage(this.img, x, y, width, height);
          }
          else if (this.type == "arc") {
            e.fillStyle = this.color;
            e.beginPath();
            e.arc(x, y, this.radius, this.startAngle, this.endAngle, this.counterClockwise);
            e.fill();
          }
          e.restore();
        }
        this.checkCollision = function(colbox, coor) {
          for (let i in this.collisionBox) {
            if (CheckCollision(this.collisionBox[i], new Coor(this.x, this.y), colbox, coor))
              return true;
          }
          return false;
        };
        this.drawCollisionBox = function(e) {
          e.save();
          e.strokeStyle = "green";
          e.lineWidth = 1.7;
          for (let i in this.collisionBox) {
            e.strokeRect(
              (this.collisionBox[i].x + this.x) * Canvas.scale,
              (this.collisionBox[i].y + this.y) * Canvas.scale,
              this.collisionBox[i].width * Canvas.scale,
              this.collisionBox[i].height * Canvas.scale
            );
          }
          e.restore();
        };
      }
      
      Cube.elem = new CanvasElement(20, 20, "rect");
      Cube.elem.x = 60;
      Cube.elem.y = 80;
      Cube.speedY = 0;
      Cube.elem.color = "red";
      Cube.elem.collisionBox = [
        new CollisionBox(0, 0, Cube.elem.width, Cube.elem.height)
      ];
      Cube.elem.draw(Canvas.ctx);
      
      Ground.elem = new CanvasElement(100000000, 100000000, "rect");
      Ground.elem.x = 0;
      Ground.elem.y = Ground.y;
      Ground.elem.color = Ground.color;
      Ground.elem.collisionBox = [
        new CollisionBox(0, 0, Ground.elem.width, Ground.elem.height)
      ];
      Ground.elem.draw(Canvas.ctx);
      
      /*Canvas.elem.ontouchstart = function() {
        Cube.speedY = -200;
        if (Game.start)
          Game.start = false;
      }*/

      function cubeJump() {
        if (Game.start) {
          Game.start = false;
          Game.isRunning = true;
          GameStartScreen.hidden = true;
        }
        else if (!Game.isRunning) {
          return;
        }
        Cube.speedY = -200;
      }
      
      window.ontouchstart = cubeJump;
      window.onkeydown = (e) => {
        if (e.key == " ") {
            if (!Game.isRunning && !Game.start)
                Game.startGame();
            else
                cubeJump();
        }
      };
      
      Game.startGame = function startGame() {
        Game.start = true;
        Game.duration = 0;
        Game.score = 0;
        GameScoreDisplay.innerHTML = "SCORE: " + Game.score;
        GameOverScreen.hidden = true;
        GameStartScreen.hidden = false;
        Obstacle.elems = [];
        Obstacle.holes = [];
        Cube.elem.x = 60;
        Cube.elem.y = 80;
        Game.spawnObstacleTimeOut = 0;
        requestAnimationFrame(Game.update);
      }
      
      Game.update = function update() {
        var currentTime = new Date().getTime();
        var interval = (currentTime - Game.lastUpdate) / 1000;
        Game.lastUpdate = currentTime;
        
        
        if (!Game.start) {
          Game.duration += interval * 1000;
          Cube.speedY += Game.gravity * interval;
          Cube.elem.y += Cube.speedY * interval;
        
          for (let i in Obstacle.elems) {
            Obstacle.elems[i].x -= interval * 150;
            if (Cube.elem.checkCollision(Obstacle.elems[i].collisionBox[0], new Coor(Obstacle.elems[i].x, Obstacle.elems[i].y))) {
              gameOver();
              return;
            }
            if (Obstacle.elems[i].x <= -Obstacle.elems[i].width)
              Obstacle.elems.pop();
          }
        
          Game.spawnObstacleTimeOut -= interval * 1000;
          
          for (let i in Obstacle.holes) {
            Obstacle.holes[i].x -= interval * 150;
            if (Cube.elem.checkCollision(Obstacle.holes[i].collisionBox[0], new Coor(Obstacle.holes[i].x, Obstacle.holes[i].y))) {
              Game.score++;
              Obstacle.holes.pop();
              GameScoreDisplay.innerHTML = "SCORE: " + Game.score;
              break;
            }
          }
        
          if (Cube.elem.checkCollision(Ground.elem.collisionBox[0], new Coor(0, Ground.elem.y))) {
            //Cube.elem.y = Ground.elem.y - Cube.elem.height;
            gameOver();
            return;
            // Cube.elem.y = Ground.elem.y - Cube.elem.height;
            // Cube.speedY = -Cube.speedY * 0.7;
          }
          else if (Cube.elem.y <= 0) {
            Cube.elem.y = 0;
            // Cube.speedY = -Cube.speedY * 0.7;
          }
        }
        
        Canvas.update();
        
        if (Game.spawnObstacleTimeOut <= 0) {
          Game.spawnObstacle();
          Game.spawnObstacleTimeOut = 1500;
        }
        
        requestAnimationFrame(Game.update);
      }
      
      Game.spawnObstacle = function spawnObstacle() {
        var upperHeight = Math.floor(Math.random() * 10000000) % 200;
        var holeSize = (Math.floor(Math.random() * 10000000) % 20) + 72;
        
        var elementUpper = new CanvasElement(40, upperHeight, "rect");
        elementUpper.x = Canvas.elem.width / Canvas.scale;
        elementUpper.y = 0;
        elementUpper.color = "black";
        elementUpper.collisionBox = [
          new CollisionBox(0, 0, elementUpper.width, elementUpper.height)
        ];
        
        var elementLower = new CanvasElement(40, Ground.y - (upperHeight + holeSize), "rect");
        elementLower.x = Canvas.elem.width / Canvas.scale;
        elementLower.y = upperHeight + holeSize;
        elementLower.color = "black";
        elementLower.collisionBox = [
          new CollisionBox(0, 0, elementLower.width, elementLower.height)
        ];
        
        var elementHole = new CanvasElement(5, holeSize, "rect");
        elementHole.x = Canvas.elem.width / Canvas.scale + 35;
        elementHole.y = upperHeight;
        elementHole.color = "grey";
        elementHole.collisionBox = [
          new CollisionBox(0, 0, elementHole.width, elementHole.height)
        ];
        
        Canvas.update();
        
        console.log("test");
        
        Obstacle.elems.unshift(elementUpper);
        Obstacle.elems.unshift(elementLower);
        Obstacle.holes.unshift(elementHole);
      }
      
      Canvas.update = function() {
        Canvas.clear();
        Cube.elem.draw(Canvas.ctx);
        Ground.elem.draw(Canvas.ctx);
        for (let i in Obstacle.elems) {
          Obstacle.elems[i].draw(Canvas.ctx);
        }
        if (!Debug.showCollisionBox) {
          return;
        }
        Cube.elem.drawCollisionBox(Canvas.ctx);
        for (let i in Obstacle.elems) {
          Obstacle.elems[i].drawCollisionBox(Canvas.ctx);
        }
        for (let i in Obstacle.holes) {
          Obstacle.holes[i].drawCollisionBox(Canvas.ctx);
        }
      }
      
      function gameOver() {
        Canvas.update();
        navigator.vibrate(100);
        GameOverScreen.hidden = false;
        Game.isRunning = false;
      }
    </script>
  </body>
</html>