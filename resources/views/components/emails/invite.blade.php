<div style="background-color: white; padding: 25px; border-style: solid; border-color: rgb(138, 138, 138); border-radius:20px; width: 380px; margin-left:25%">
    <div class="header">
        <div class="top">
            <h1 style="font-weight:800; margin-left:48px; color:black; font-family: 'Arial'">Convite para Gestão:</h1>            
            <h2 style="font-weight:800; margin-left:130px; color:black; font-family: 'Arial'">
                {{ session()->get('restaurant.name') }}</h3>
        </div>
    </div>
    <hr><br>
    <div class="body"><br>
        <span style="color: rgb(27, 27, 27); font-family: 'Arial'">Foi convidado para ajudar na gestão
            do restaurante
            {{ session()->get('restaurant.name') }}</span><br />
        <button
            style="background-color: #1C46B2; padding: 10px; border-radius:100px; margin-left:140px; margin-top: 15px; margin-bottom: 15px;"><a
                style="color: white; text-decoration: none; font-family: 'Arial'" href="http://127.0.0.1:8000/register">Aceitar
                Convite</a></button><br />
        <span style="color: rgb(24, 24, 24)">Convite enviado por:
            {{ session()->get('user.firstName') . ' ' . session()->get('user.lastName') }}</span><br>
        <span style="color:rgb(85, 85, 85); margin-top:5px">Se acha que foi um engano, ignore este email</span>
    </div>
    <hr><br>
    <div class="footer">
        <span style="color: rgb(59, 59, 59)">EasyOrder</span>
    </div>
</div>
