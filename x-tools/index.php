<?php
session_start();

// Password Hashing Logic
$generatedHash = "";
if (isset($_POST['password_to_hash'])) {
    $generatedHash = password_hash($_POST['password_to_hash'], PASSWORD_DEFAULT);
}

// Clear Session Logic
if (isset($_GET['action']) && $_GET['action'] == 'clear_session') {
    session_destroy();
    header("Location: index.php");
    exit;
}

// Recursive Renderer for Session Data
function renderSessionData($data) {
    if (is_array($data) || is_object($data)) {
        if (empty((array)$data)) return '<span class="text-gray-600 italic">[]</span>';
        
        $html = '<div class="flex flex-col gap-1.5 w-full">';
        foreach ($data as $key => $value) {
            $html .= '<div class="flex items-stretch border border-white/5 rounded-md overflow-hidden bg-white/2 hover:border-blue-500/30 transition-colors">';
            $html .= '<div class="bg-blue-500/10 px-3 py-1 text-[10px] font-mono uppercase tracking-wider text-blue-400 border-r border-white/5 min-w-[120px] flex items-center">' . htmlspecialchars($key) . '</div>';
            $html .= '<div class="px-3 py-1 flex-grow flex items-center overflow-x-auto scrollbar-hide">' . renderSessionData($value) . '</div>';
            $html .= '</div>';
        }
        $html .= '</div>';
        return $html;
    } else {
        if (is_bool($data)) return '<span class="text-fuchsia-500 font-bold text-xs">' . ($data ? 'TRUE' : 'FALSE') . '</span>';
        if (is_null($data)) return '<span class="text-red-500 italic text-xs">NULL</span>';
        return '<span class="text-blue-300 font-medium text-sm break-all font-mono">' . htmlspecialchars($data) . '</span>';
    }
}

?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>X-Tools | Dev Suite</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        neon: {
                            blue: '#0066ff',
                            purple: '#6600ff',
                            green: '#00ffaa',
                        },
                        deep: {
                            bg: '#020205',
                            card: '#0a0a12',
                        }
                    },
                    fontFamily: {
                        orbitron: ['Orbitron', 'sans-serif'],
                        inter: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer base {
            body {
                @apply bg-deep-bg text-gray-300 font-inter min-h-screen;
                background-image: 
                    radial-gradient(circle at 5% 5%, rgba(0, 102, 255, 0.08) 0%, transparent 35%),
                    radial-gradient(circle at 95% 95%, rgba(102, 0, 255, 0.08) 0%, transparent 35%);
            }
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .glass {
            @apply bg-white/[0.03] backdrop-blur-2xl border border-white/10;
        }
        .floating-header {
            @apply fixed top-4 left-4 right-4 z-50 glass rounded-2xl px-6 py-3 flex items-center justify-between shadow-2xl shadow-black/50;
        }
    </style>
</head>
<body class="flex flex-col items-center pt-24">

<header class="floating-header">
    <div class="flex items-center gap-4">
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-neon-blue to-neon-purple flex items-center justify-center shadow-lg shadow-neon-blue/20">
            <i class="fa-solid fa-terminal text-white text-sm"></i>
        </div>
        <div>
            <h1 class="font-orbitron text-sm md:text-base tracking-[2px] uppercase text-white leading-none">
                X-TOOLS <span class="text-neon-blue">SYSTEM</span>
            </h1>
            <p class="text-[8px] text-gray-500 tracking-[2px] uppercase mt-1">Interface v1.0.6</p>
        </div>
    </div>
    <div class="hidden md:flex items-center gap-6">
        <div class="flex items-center gap-2 text-[10px] text-gray-500 uppercase tracking-widest">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_#10b981] animate-pulse"></span>
            Node: Secure
        </div>
        <div class="h-4 w-px bg-white/10"></div>
        <div class="text-[10px] text-gray-500 uppercase tracking-widest"><?php echo date('H:i:s'); ?> UTC</div>
    </div>
</header>

<main class="w-full max-w-[1500px] px-6 mb-10 flex flex-col lg:flex-row gap-6">
    <!-- Sidebar -->
    <aside class="w-full lg:w-[320px] flex flex-col gap-6">
        <!-- Hasher -->
        <section class="glass rounded-2xl p-5 hover:border-neon-blue/30 transition-all duration-300">
            <h2 class="font-orbitron text-[10px] text-neon-blue mb-4 flex items-center gap-2 border-b border-white/5 pb-3 uppercase tracking-widest">
                <i class="fa-solid fa-key"></i> BCRYPT HASHER
            </h2>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-[9px] text-gray-500 uppercase tracking-wider mb-2">Input Plaintext</label>
                    <input type="text" name="password_to_hash" placeholder="..." required 
                           class="w-full bg-black/40 border border-white/10 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-neon-blue focus:ring-1 focus:ring-neon-blue/30 transition-all text-white">
                </div>
                <button type="submit" class="w-full bg-neon-blue text-white py-2 rounded-lg text-[10px] font-orbitron uppercase tracking-widest hover:brightness-110 active:scale-95 transition-all shadow-lg shadow-neon-blue/20">
                    Generate
                </button>
            </form>

            <?php if ($generatedHash): ?>
            <div class="mt-4 p-3 bg-black/50 border-l-2 border-neon-blue rounded-r-lg relative group">
                <label class="block text-[8px] text-neon-blue uppercase mb-1">Hash Output</label>
                <code id="hashValue" class="text-[10px] text-blue-300 break-all block pr-6 font-mono"><?php echo $generatedHash; ?></code>
                <button onclick="copyHash()" class="absolute top-2 right-2 text-gray-500 hover:text-white transition-colors">
                    <i class="fa-regular fa-copy text-[10px]"></i>
                </button>
            </div>
            <?php endif; ?>
        </section>

        <!-- Environment Metrics (Moved here) -->
        <section class="glass rounded-2xl p-5 border-l-2 border-l-neon-purple">
            <h2 class="font-orbitron text-[10px] text-neon-purple mb-4 flex items-center gap-2 border-b border-white/5 pb-3 uppercase tracking-widest">
                <i class="fa-solid fa-circle-info text-xs"></i> ENV METRICS
            </h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-[8px] text-gray-500 uppercase tracking-widest mb-1">PHP Engine</label>
                    <div class="text-xs font-bold text-white"><?php echo PHP_VERSION; ?></div>
                </div>
                <div>
                    <label class="block text-[8px] text-gray-500 uppercase tracking-widest mb-1">Host Server</label>
                    <div class="text-[10px] font-medium text-gray-400 break-all leading-relaxed"><?php echo $_SERVER['SERVER_SOFTWARE']; ?></div>
                </div>
                <div>
                    <label class="block text-[8px] text-gray-500 uppercase tracking-widest mb-1">Base Path</label>
                    <div class="text-[9px] font-medium text-gray-500 break-all leading-tight"><?php echo $_SERVER['DOCUMENT_ROOT']; ?></div>
                </div>
            </div>
        </section>
    </aside>

    <!-- Content Area -->
    <section class="flex-grow flex flex-col gap-6">
        <div class="glass rounded-2xl p-5">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-5 border-b border-white/5 pb-4">
                <h2 class="font-orbitron text-[10px] text-neon-blue flex items-center gap-2 uppercase tracking-widest">
                    <i class="fa-solid fa-database text-xs"></i> SESSION INSPECTOR 
                    <span class="bg-neon-purple/20 text-neon-purple border border-neon-purple/30 px-2 py-0.5 rounded text-[8px] ml-2"><?php echo count($_SESSION); ?> ACTIVE KEYS</span>
                </h2>
                <div class="flex items-center gap-2">
                    <a href="index.php" class="bg-white/5 border border-white/10 hover:border-neon-blue/50 text-[9px] font-orbitron uppercase px-3 py-1.5 rounded-lg flex items-center gap-2 transition-all text-gray-400 hover:text-white">
                        <i class="fa-solid fa-rotate"></i> Reload
                    </a>
                    <button onclick="if(confirm('Wipe session data?')) window.location.href='?action=clear_session'" 
                            class="bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500 hover:text-white text-[9px] font-orbitron uppercase px-3 py-1.5 rounded-lg transition-all">
                        <i class="fa-solid fa-trash-can mr-1"></i> Wipe
                    </button>
                </div>
            </div>

            <?php if (empty($_SESSION)): ?>
                <div class="py-20 text-center bg-black/30 rounded-xl border border-dashed border-white/5">
                    <i class="fa-solid fa-ghost text-3xl text-gray-700 mb-4 block"></i>
                    <p class="text-gray-500 text-[10px] uppercase tracking-[4px]">Empty Session State</p>
                </div>
            <?php else: ?>
                <div class="flex flex-col gap-3">
                    <?php foreach ($_SESSION as $key => $value): ?>
                    <div class="glass rounded-xl overflow-hidden group hover:border-neon-blue/20 transition-all">
                        <div class="bg-white/[0.02] px-4 py-1.5 flex items-center justify-between border-b border-white/5">
                            <span class="font-orbitron text-[9px] text-neon-blue uppercase tracking-widest"><?php echo htmlspecialchars($key); ?></span>
                            <i class="fa-solid fa-code text-[8px] text-gray-700 group-hover:text-neon-blue transition-colors"></i>
                        </div>
                        <div class="p-2 bg-black/10">
                            <?php echo renderSessionData($value); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<script>
    function copyHash() {
        const hashText = document.getElementById('hashValue').innerText;
        navigator.clipboard.writeText(hashText).then(() => {
            const btn = document.querySelector('button i.fa-copy');
            const originalClass = btn.className;
            btn.className = 'fa-solid fa-check text-blue-300';
            setTimeout(() => {
                btn.className = originalClass;
            }, 2000);
        });
    }
</script>

</body>
</html>
