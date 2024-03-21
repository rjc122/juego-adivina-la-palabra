<?php
session_start();

class JuegoAdivinanza {
    private $palabrasPosibles;
    private $palabraDelDia;
    private $palabrasConPistas;
    public $fallas;
    public $historial;

    public function __construct() {
        $this->palabrasPosibles = ['programacion', 'desarrollo', 'aplicacion', 'algoritmo', 'tecnologia'];
        $this->fallas = isset($_SESSION['fallas']) ? $_SESSION['fallas'] : 5;
        $this->historial = isset($_SESSION['historial']) ? $_SESSION['historial'] : [];
        $this->palabraDelDia = isset($_SESSION['palabraDelDia']) ? $_SESSION['palabraDelDia'] : '';

        $this->palabrasConPistas = [
            'programacion' => [
                "Es una disciplina que implica la creación de software y aplicaciones.",
                "Implica la resolución de problemas y la creación de algoritmos.",
                "Es fundamental en la creación de sitios web y aplicaciones móviles.",
                "Es un componente esencial en la automatización de tareas",
                "Los programas informáticos son creados a través de este proceso",
                "Se emplea en una amplia variedad de campos, desde videojuegos hasta inteligencia artificial",
            ],
            'desarrollo' => [
                "Implica la creación y mejora de software.",
                "Incluye el diseño de aplicaciones y sistemas informáticos.",
                "Es un proceso creativo y técnico a la vez.",
                "Involucra la planificación y la programación.",
                "Se enfoca en la eficiencia y la calidad del software.",
                "Los desarrolladores pueden utilizar varios lenguajes de programación.",
            ],
            'aplicacion' => [
                "Es un programa informático diseñado para realizar tareas específicas.",
                "Pueden ser aplicaciones de escritorio o móviles.",
                "Las aplicaciones móviles son populares en dispositivos como smartphones y tabletas.",
                "Existen aplicaciones para una amplia variedad de usos, desde productividad hasta entretenimiento.",
                "El desarrollo de aplicaciones es una parte importante de la industria de la tecnología.",
                "Las aplicaciones pueden ser gratuitas o de pago.",
            ],
            'algoritmo' => [
                "Es un conjunto de pasos para resolver un problema o realizar una tarea.",
                "Los algoritmos son parte fundamental de la programación.",
                "Deben ser precisos y eficientes.",
                "Se pueden representar mediante diagramas de flujo o pseudocódigo.",
                "Los algoritmos son esenciales en campos como la inteligencia artificial y la criptografía.",
                "La optimización de algoritmos es una disciplina en sí misma.",
            ],
            'tecnologia' => [
                "Incluye dispositivos y sistemas basados en la ciencia y la ingeniería.",
                "La tecnología está en constante evolución.",
                "Abarca campos como la electrónica, la informática y las comunicaciones.",
                "La tecnología tiene un impacto profundo en la sociedad y la economía.",
                "La innovación tecnológica es un motor importante del progreso.",
                "La industria de la tecnología es una de las más grandes y competitivas del mundo.",
            ],
        ];

        if (empty($this->palabraDelDia)) {
            $this->elegirPalabraDelDia();
        }
    }

    public function jugar() {
        if (isset($_POST['adivinanza'])) {
            if ($this->fallas > 0) {
                $adivinanza = strtolower($_POST['adivinanza']);
                if (!empty($adivinanza)) {
                    if ($adivinanza === $this->palabraDelDia) {
                        echo '<div class="success">¡Felicidades! Adivinaste la palabra del día.</div>';
                        echo '<div id="confetti-container">';
                        $colores = ["#fbc02d", "#ff5722", "#e91e63", "#2196f3", "#4caf50"];
                        for ($i = 0; $i < 50; $i++) {
                            $color = $colores[array_rand($colores)];
                            echo '<div class="confetti" style="background-color: ' . $color . '; top: ' . rand(0, 100) . 'vh; left: ' . rand(0, 100) . 'vw; animation-delay: ' . (rand(0, 3000) / 1000) . 's;"></div>';
                        }

                   
                        echo '<script>';
                        echo 'var winAudio = new Audio("win.mp3");';
                        echo 'winAudio.play();';
                        echo '</script>';

                        echo '</div>';
                   
                        $this->elegirPalabraDelDia();
                    } else {
                        $this->historial[] = $adivinanza;
                        $_SESSION['historial'] = $this->historial;
                        $this->fallas--;
                        $_SESSION['fallas'] = $this->fallas;
                        echo  '<div class="error">¡Incorrecto! Te quedan ' . ($this->fallas === 0 ? 'Vida extra' : $this->fallas) . ' intentos.</div>';
                    }
                } else {
                    echo '<div class="error">¡Por favor, ingresa una palabra antes de intentar adivinar!</div>';
                }
            } else {
                echo '<div class="error">¡Perdiste! La palabra correcta era: ' . $this->palabraDelDia . '</div>';

                echo '<script>';
                echo 'var loseAudio = new Audio("lose.mp3");';
                echo 'loseAudio.play();';
                echo '</script>';

                $this->elegirPalabraDelDia();
            }
        }

        if (isset($_POST['reiniciar'])) {
            $this->reiniciarJuego();
        }
    }

    public function generarInformacion() {
        $pistas = $this->palabrasConPistas[$this->palabraDelDia];
        $pista = $pistas[array_rand($pistas)];
        return $pista;
    }

    private function elegirPalabraDelDia() {
        $this->palabraDelDia = $this->palabrasPosibles[array_rand($this->palabrasPosibles)];
        $_SESSION['palabraDelDia'] = $this->palabraDelDia;
    }

    private function reiniciarJuego() {
        $this->fallas = 5;
        $this->historial = [];
        $_SESSION['fallas'] = $this->fallas;
        $_SESSION['historial'] = $this->historial;
        session_destroy();
        session_start();
        $this->elegirPalabraDelDia();
    }
}

$juego = new JuegoAdivinanza();
$juego->jugar();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="styles.css">
    <title>Juego de Adivinar la Palabra</title>
</head>
<body>
    <h1>Juego de Adivinar la Palabra del Día</h1>
    <p>Intenta adivinar la palabra completa.</p>

    <p>Historial de fallas: <?php echo is_array($juego->historial) ? implode(", ", $juego->historial) : ''; ?></p>
    <p>Intentos restantes: <?php echo ($juego->fallas === 0) ? 'Vida extra' : $juego->fallas; ?></p>

    <form method="post">
    <?php if ($juego->fallas < 5): ?>
        <?php if ($juego->fallas > 1): ?>
            <span class="pista">Pista: <?php echo $juego->generarInformacion(); ?></span>
        <?php else: ?>
            <p class="advertencia">⚠ Te queda un intento.</p>
        <?php endif; ?>
    <?php endif; ?>
    </form>

    <form method="post">
        <input type="text" name="adivinanza" placeholder="Ingresa una palabra...">
        <input type="submit" name="intentar" value="Intentar">
    </form>

    <form method="post">
        <input type="submit" name="reiniciar" value="Reiniciar Juego">
    </form>

    <audio id="win-sound" preload="auto">
        <source src="win.mp3" type="audio/mpeg">
    </audio>
</body>
</html>
