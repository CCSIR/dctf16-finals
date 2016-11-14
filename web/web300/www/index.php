<!DOCTYPE html>
<html>
<head>
    <title>Code sandbox</title>
    <style type="text/css" media="screen">
        h1 {
            text-align: center;
            font-family: monospace;
        }
        #editor, #output {
            height: 300px;
            width: 700px;
            max-width: 50%;
            margin: 0 auto;
            font-size: 14px;
            padding: 15px;
        }
        #output {
            border: dashed 1px #ccc;
            white-space: pre-wrap;
        }
        #submit {
            display: block;
            width: 730px;
            height: 50px;
            line-height: 50px;
            max-width: calc(50% + 30px);
            margin: 15px auto;
            font-size: 22px;
            color: #333;
            background-color: #eee;
            border: solid 1px #ccc;
            outline: 0;
        }
        #submit:hover {
            background-color: #fff;
        }
        #submit:active {
            background-color: #e0e0e0;
        }
    </style>
</head>
<body>

    <h1>Secure PHP sandbox</h1>

    <div id="editor" name="code">
function foo($i) {
    echo $i;
}

foo(1337);
</div>

<button id="submit" type="submit" onclick="run();">RUN</button>

<pre id="output"></pre>

    <script src="ace/ace.js" type="text/javascript" charset="utf-8"></script>
    <script>
        var editor = ace.edit("editor");
        editor.setTheme("ace/theme/monokai");
        editor.getSession().setMode({ path: "ace/mode/php", inline: true });
        function run() {
            var request = new XMLHttpRequest();
            request.open('POST', 'sandbox.php', true);
            request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            request.send('code=' + encodeURIComponent(editor.getValue()));
            request.onload = function() {
              if (request.status >= 200 && request.status < 400) {
                document.getElementById("output").innerText = request.responseText;
              } else {
                document.getElementById("output").innerText = '[error: server returned error]';
              }
            };
        }
    </script>

</body>
</html>
