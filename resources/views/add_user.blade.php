<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Add User</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios@1.1.2/dist/axios.min.js"></script>

    <script src="https://unpkg.com/@vueuse/shared"></script>
    <script src="https://unpkg.com/@vueuse/core"></script>


</head>
<body class="d-flex flex-column h-100">

<!-- Begin page content -->
<main class="flex-shrink-0">
    <div class="container py-4">

        <header>
            <!-- Fixed navbar -->
            <nav class="navbar navbar-expand-md navbar-dark bg-dark">
                <div class="container-fluid">

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarCollapse">
                        <ul class="navbar-nav me-auto mb-2 mb-md-0">
                            <li class="nav-item">
                                <a class="nav-link" href="/">Users</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="/users/add">Add User</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>

        <h1 class="mt-5">Add User</h1>


        <div id="app">
            <form action="" @submit.prevent="onSubmit">
                <div class="mb-3">
                    <label for="inputName" class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" id="inputName">
                    <div class="invalid-feedback">
                        Please provide a valid city.
                    </div>
                </div>
                <div class="mb-3">
                    <label for="inputEmail" class="form-label">Email</label>
                    <input type="text" name="email" class="form-control" id="inputEmail">
                </div>
                <div class="mb-3">
                    <label for="inputPosition" class="form-label">Position</label>
                    <select class="form-select" name="position_id" id="inputPosition">
                        <option selected>Select...</option>
                        <option v-for="pos in positions" :value="pos.id" :key="pos.id">@{{ pos.name }}</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="inputPhone" class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" id="inputPhone">
                </div>
                <div class="mb-3">
                    <label for="inputPhoto" class="form-label">Photo</label>
                    <input type="file" name="photo_raw" class="form-control" id="inputPhoto">
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>

        </div>
    </div>
    <script>



        const usePositions = (function () {
            const list = ref([]);
            const isFetching = ref(true);
            const error = ref(null);

            axios
                .get('/api/v1/positions')
                .then((response) => {
                    response.data.positions.forEach(pos => list.value.push(pos));
                })
                .catch((e) => {
                    error.value = e;
                })
                .finally(() => {
                    isFetching.value = false;
                });

            return {positions: list, isFetching, error};
        });

        const { createApp, ref } = Vue;

        const rootComponent = {
            setup() {

                const { positions } = usePositions();


                return {
                    positions,
                }
            },
            methods: {
                onSubmit(event) {
                    const formData = new FormData(event.target);
                    axios
                        .post('/api/v1/users', formData)
                        .then(
                            (response) => {
                                console.log({postResponse: response})
                            },
                            (e) => {
                                if (e.response.data.success === false) {

                                }
                                console.log({postError: e})
                            }
                        );
                },
            }
        };

        createApp(rootComponent).mount('#app')
    </script>
</main>

<footer class="footer mt-auto py-3 bg-body-tertiary">
    <div class="container">
        <span class="text-body-secondary"> </span>
    </div>
</footer>
<script src="/docs/5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>



</body>

</html>
