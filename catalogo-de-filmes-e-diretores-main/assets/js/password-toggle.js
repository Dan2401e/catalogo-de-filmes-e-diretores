// Ativa os controles de mostrar/ocultar senha nas telas de autenticação.
document.querySelectorAll('.password-toggle').forEach((botao) => {
    const campo = botao.closest('.password-field')?.querySelector('input[type="password"], input[type="text"]');

    if (!campo) {
        return;
    }

    botao.hidden = false;
    botao.addEventListener('click', () => {
        const mostrarSenha = campo.type === 'password';

        campo.type = mostrarSenha ? 'text' : 'password';
        botao.textContent = mostrarSenha ? 'Ocultar' : 'Mostrar';
        botao.setAttribute('aria-label', `${mostrarSenha ? 'Ocultar' : 'Mostrar'} senha`);
        botao.setAttribute('aria-pressed', String(mostrarSenha));
    });
});
