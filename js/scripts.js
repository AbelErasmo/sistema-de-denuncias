document.addEventListener("DOMContentLoaded", () => {
    const menu = document.querySelector(".navbar ul");
    const menuIcon = document.querySelector(".menu");
    const fecharIcon = document.querySelector(".fechar-menu");

    function menuResponsivo(mostarMenu) {
        menu.classList.toggle("active", mostarMenu);
        menuIcon.style.display = mostarMenu ? "none" : "block";
        fecharIcon.style.display = mostarMenu ? "block" : "none";
    }

    menuResponsivo(false);

    menuIcon.addEventListener("click", () => menuResponsivo(true));
    fecharIcon.addEventListener("click", () => menuResponsivo(false));
    menu.querySelectorAll("a").forEach(link => link.addEventListener("click", () => menuResponsivo(false)));

    // Fecha o menu ao clicar fora
    document.addEventListener("click", (e) => {
        if (
            menu.classList.contains("active") &&
            !menu.contains(e.target) &&
            !menuIcon.contains(e.target) &&
            !fecharIcon.contains(e.target)
        ) {
            menuResponsivo(false);
        }
    });

    const form = document.getElementById("denuncia-form");
    const formProtocolo = document.getElementById("historico-form");

    if (form) {
        form.addEventListener("submit", (e) => {
            if (!denuncia.validacao()) {
                e.preventDefault();
            } else {
                e.preventDefault();
                form.reset();
            }
        });
    }

    if (formProtocolo) {
         formProtocolo.addEventListener("submit", (e) => {
            if (!denuncia.validacao()) {
                e.preventDefault();
            } else {
                e.preventDefault();
                formProtocolo.reset();
            }
        });
    }
});

class Denuncia {
    constructor() {
        this.loader = null;
        this.criarLoader();
        this.container = document.querySelector("#container");
    }

    criarLoader() {
        if (!document.getElementById("global-loader")) {
            this.loader = document.createElement("div");
            this.loader.id = "global-loader";
            this.loader.innerHTML = '<div class="spinner" role="status" aria-live="polite" aria-label="Carregando..."></div>';
            this.loader.style.cssText = "display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(255,255,255,0.7);z-index:9999;justify-content:center;align-items:center;";
            document.body.appendChild(this.loader);
        } else {
            this.loader = document.getElementById("global-loader");
        }
    }

    alterarMenu(targetFormId) {
        const formularios = ['denuncia-form', 'historico-form', 'protocolo-form', 'historico'];
        const targetForm = document.getElementById(targetFormId);

        this.loader.style.display = "flex";

        setTimeout(() => {
            formularios.forEach(formId => {
                const form = document.getElementById(formId);
                if (form) {
                    form.style.display = 'none';
                }
            });

            if (targetForm) {
                targetForm.style.display = 'block';
            }

            this.loader.style.display = "none";
        }, 600);
    }

    novaDenuncia() {
        this.alterarMenu("denuncia-form");
    }

    historico() {
        this.alterarMenu("historico-form");
    }

    mostrarHistorico() {
        this.alterarMenu("historico");
    }
    
    protocolo() {
        this.alterarMenu("protocolo-form");
    }

    validacao() {
        const campos = document.querySelectorAll(".obrigatorio");
        let valido = true;
    
        campos.forEach(input => {
            let erro = input.nextElementSibling;
            if (!erro || !erro.classList.contains("error")) {
                erro = document.createElement("span");
                erro.className = "error";
                input.insertAdjacentElement("afterend", erro);
            }
            if (!input.value.trim()) {
                erro.textContent = "*Campo obrigatório";
                input.classList.add("invalido");
                valido = false;
                input.focus();
            } else {
                erro.textContent = "";
                input.classList.remove("invalido");
            }
        });

        if (!valido) {
            event.preventDefault()
        }

        return valido;
    }

     adicionarInput() {
        if(!this.container) {
            console.error("Elemento '#container' não encontrado."); 
            return;

        }
        const novoInput = document.createElement("input");
        novoInput.type = "file";
        novoInput.id = "file";
        novoInput.name = "file[]";
        novoInput.className = "file-input obrigatorio";

        const anexosAtuais = this.container.querySelectorAll('input[type="file"].file-input').length;
        if (anexosAtuais >= 4) {
            alert("Só pode adicionar até 5 anexos.");
            return;
        }
        this.container.appendChild(novoInput);
    }

    removerInput() {
        if (!this.container) {
            console.error("Elemento '#container' não encontrado."); 
            return;
        }

        const inputs = this.container.querySelectorAll('input[type="file"].file-input');
        if (inputs.length <= 0) {
            alert("Deve adicionar pelo menos um anexo");
            return;
        }

        const ultimoInput = inputs[inputs.length - 1];

        // Encontra o span.error que está imediatamente após o input
        const erro = ultimoInput.nextElementSibling;
        if (erro && erro.classList.contains("error")) {
            erro.remove();
        }

        ultimoInput.remove();
    }


    anonimato() {
        const sim = document.getElementById("sim");
        const ocultar = document.getElementById("dados-pessoais");
        const adicionarClass = document.querySelectorAll(".validacao");

        if (sim && ocultar) {
            if (sim.checked) {
                ocultar.style.display = "none";
            } else {
                ocultar.style.display = "block";
                adicionarClass.forEach(el => el.className = "obrigatorio");

            }
        }
    }

}
const denuncia = new Denuncia();