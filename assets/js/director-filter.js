const directorSelect = document.querySelector('#director-id');
const genreSelect = document.querySelector('#genero');
const directorStatus = document.querySelector('#director-status');

if (directorSelect && genreSelect && !directorSelect.disabled) {
    const allGenres = Array.from(genreSelect.options, (option) => ({
        label: option.textContent,
        value: option.value,
    }));
    const fetchJson = async (url) => {
        const response = await fetch(url);
        const data = await response.json();
        if (!response.ok) throw new Error(data.error || 'Não foi possível carregar os dados.');
        return data;
    };

    const loadGenres = async (directorId, selectedGenre = '') => {
        if (!directorId) return;
        const url = new URL(directorSelect.dataset.genresUrl, window.location.href);
        url.searchParams.set('diretor_id', directorId);

        genreSelect.disabled = true;
        try {
            const data = await fetchJson(url);
            genreSelect.replaceChildren(new Option('Todos os gêneros', ''));
            for (const genre of data.results) {
                genreSelect.add(new Option(genre.name, String(genre.id), false, String(genre.id) === selectedGenre));
            }
            if (directorStatus) directorStatus.textContent = `${data.results.length} gêneros encontrados para este diretor.`;
        } catch (error) {
            genreSelect.replaceChildren(new Option('Falha ao carregar gêneros', ''));
            if (directorStatus) directorStatus.textContent = error.message || 'Falha ao carregar gêneros do diretor.';
            console.error(error);
        } finally {
            genreSelect.disabled = false;
        }
    };

    if (directorSelect.value) {
        loadGenres(directorSelect.value, genreSelect.value);
    }

    directorSelect.addEventListener('change', () => {
        if (!directorSelect.value) {
            genreSelect.replaceChildren(...allGenres.map((genre) => new Option(genre.label, genre.value)));
            if (directorStatus) directorStatus.textContent = 'Selecione um diretor para ver os gêneros dos filmes dele.';
            return;
        }
        loadGenres(directorSelect.value);
    });

    const popularUrl = directorSelect.dataset.popularUrl;
    if (popularUrl) {
        fetchJson(popularUrl)
            .then((data) => {
                const selectedId = directorSelect.value;
                directorSelect.replaceChildren(new Option('Todos os diretores', ''));
                for (const director of data.results) {
                    directorSelect.add(new Option(director.name, String(director.id), false, String(director.id) === selectedId));
                }
                if (selectedId && !data.results.some((director) => String(director.id) === selectedId)) {
                    directorSelect.add(new Option(directorSelect.dataset.selectedName, selectedId, true, true));
                }
                if (directorStatus) {
                    directorStatus.textContent = `${data.results.length} diretores de filmes populares disponíveis.`;
                }
            })
            .catch((error) => {
                if (directorStatus) directorStatus.textContent = error.message || 'Não foi possível carregar os diretores populares.';
                console.error(error);
            });
    }
}
