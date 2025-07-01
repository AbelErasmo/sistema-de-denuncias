document.addEventListener("DOMContentLoaded", () => {
    const menu = document.querySelector(".navbar ul");
    const menuIcon = document.querySelector(".menu");
    const fecharIcon = document.querySelector(".fechar-menu");
    const form = document.getElementById("denuncia-form");
    const formProtocolo = document.getElementById("historico-form");

    function menuResponsivo(mostrarMenu) {
        menu.classList.toggle("active", mostrarMenu);
        menuIcon.style.display = mostrarMenu ? "none" : "block";
        fecharIcon.style.display = mostrarMenu ? "block" : "none";
    }

    menuResponsivo(false);

    menuIcon.addEventListener("click", () => menuResponsivo(true));
    fecharIcon.addEventListener("click", () => menuResponsivo(false));
    menu.querySelectorAll("a").forEach(link => link.addEventListener("click", () => menuResponsivo(false)));

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

        if (form) {
            form.addEventListener("submit", async function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                const loader = document.getElementById("global-loader");
                if (loader) loader.style.display = "flex";

                try {
                    if (!denuncia.validacao()) {
                        if (loader) loader.style.display = "none"; // garante que o loader some mesmo se os campos forem inválidos
                        return;
                    }

                    const response = await fetch(this.action, {
                        method: "POST",
                        body: formData
                    });
                    
                    const rawText = await response.text(); // lê uma única vez o json
                    console.log("Resposta bruta do servidor:", rawText); // somente para debug
                    
                    let result;
                    try {
                        result = JSON.parse(rawText);          // tenta converter para JSON
                    } catch (error) {
                        console.error("Erro ao interpretar resposta:", error);
                        alert("Resposta inválida do servidor. Verifique se o PHP está retornando JSON.");
                        return;
                    }

                    if (result.status === 'success') {
                        document.getElementById("retorno-protocolo").innerHTML = `
                            <p>Denúncia foi registada com sucesso.</p>
                            <p><strong>O seu número de protocolo é: </strong> ${result.protocolo}</p>
                            <p>Guarde-o em local seguro. Não partilhe, para preservar o anonimato.</p>
                        `;
                        form.reset();
                    } else {
                        alert(result.message || "Ocorreu um erro ao registar denúncia.");
                    }

                } catch (err) {
                    console.error(err);
                    alert("Erro na comunicação com o servidor. Verifique sua conexão.");
                } finally {
                    if (loader) loader.style.display = "none";
                }
            });
        }

    if (formProtocolo) {
        formProtocolo.addEventListener("submit", (e) => {
            e.preventDefault();
            if (denuncia.validacao()) {
                formProtocolo.reset();
            }
        });
    }
});

document.getElementById("consultar-btn").addEventListener('click', async() => {
    const protocolo = document.getElementById("protocolo").value.trim();
    const msg = document.getElementById("msg");

    if (!/^DNC-\d{8}-[A-F0-9]{6}$/i.test(protocolo)) {
        // msg.innerText = 'Insira um numero de protocolo válido';
        // console.log("Insira um numero de protocolo válido")
        return;
    }

    try {
        const response = await fetch(`./admin/consultar_denuncia.php?protocolo=${encodeURIComponent(protocolo)}`);
        const data = await response.json();

        if (data.status === "success") {
            denuncia.exibirDenunciaNaTabela(data.dados);
        } else {
            exibirMensagemVazia("Nenhuma denuncia encontrada com esse protocolo.");
        }
    } catch (err) {
        exibirMensagemVazia("Erro ao consultar denúncia.");
        console.error(err);
    }

    function exibirMensagemVazia(mensagem) {
        const tbody = document.getElementById('historico-body');
        tbody.innerHTML = `
            <tr>
                <td colspan="5" style="text-align: center;">${mensagem}</td>
            </tr>
        `;
    }
});

class Denuncia {
    constructor() {
        this.loader = null;
        this.container = document.querySelector("#container");
        this.criarLoader();
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
                if (form) form.style.display = 'none';
            });

            if (targetForm) targetForm.style.display = 'block';
            this.loader.style.display = "none";
        }, 600);
    }

    novaDenuncia() { this.alterarMenu("denuncia-form"); }
    historico() { this.alterarMenu("historico-form"); }
    mostrarHistorico() { this.alterarMenu("historico"); }
    protocolo() { this.alterarMenu("protocolo-form"); }

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

        return valido;
    }
    
    adicionarInput() {
        if (!this.container) {
            console.error("Elemento '#container' não encontrado.");
            return;
        }

        const anexosAtuais = this.container.querySelectorAll('input[type="file"].file-input').length;
        if (anexosAtuais >= 5) {
            alert("Você só pode adicionar até 5 anexos.");
            return;
        }

        const novoInput = document.createElement("input");
        novoInput.type = "file";
        novoInput.name = "file[]";
        novoInput.className = "file-input obrigatorio";
        this.container.appendChild(novoInput);
    }

    removerInput() {
        if (!this.container) {
            console.error("Elemento '#container' não encontrado.");
            return;
        }

        const inputs = this.container.querySelectorAll('input[type="file"].file-input');
        if (inputs.length <= 0) {
            alert("Deve adicionar pelo menos um anexo.");
            return;
        }

        const ultimoInput = inputs[inputs.length - 1];
        const erro = ultimoInput.nextElementSibling;
        if (erro && erro.classList.contains("error")) erro.remove();

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

    exibirDenunciaNaTabela(denuncia) {
        const tbody = document.getElementById("historico-body");
        // const mostrar_tabela = document.getElementById("historico-form");
        // Remove o conteudo anterior
        tbody.innerHTML = "";

        const linha = document.createElement("tr");
        linha.innerHTML = `
            <td>${denuncia.protocolo}</td>
            <td>${denuncia.tipo_denuncia}</td>
            <td>${denuncia.descricao}</td>
            <td>${denuncia.data_denuncia}</td>
            <td><span class="status-${denuncia.status}">${denuncia.status}</span></td>
        `;
        // mostrar_tabela.style.display = "block";
        tbody.appendChild(linha);
    }

    formatarData(data) {
    const d = new Date(data);
    return isNaN(d) ? data : d.toLocaleString("pt-BR", {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}
}

const denuncia = new Denuncia();
