<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Users</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

        <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
        <script src="https://unpkg.com/axios@1.1.2/dist/axios.min.js"></script>


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
                <a class="nav-link active" aria-current="page" href="/">Users</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/users/add">Add User</a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>

    <h1 class="mt-5">Users</h1>


    <div id="app">

            <table class="table">
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Position</th>
                    <th>Photo</th>
                </tr>
                <tr v-for="user in userList" :key="user.id">
                    <td>@{{ user.id }}</td>
                    <td>@{{ user.name }}</td>
                    <td>@{{ user.email }}</td>
                    <td>@{{ user.position }}</td>
                    <td><img :src="user.photo" alt=""></td>
                </tr>
            </table>
            <button type="button" class="btn btn-primary" :disabled="!hasMore" @click="loadMore">Load more</button>

        </div>
    </div>
    <script>
        const { createApp, ref } = Vue
        const rootComponent = {
            setup() {
                const userList = ref([]);
                const nextUrl = ref('/api/v1/users');

                return {
                    userList,
                    response: null,
                    nextUrl,
                }
            },
            mounted() {
                this.loadMore();
            },
            computed: {
                hasMore() {
                    return this.nextUrl !== null;
                }
            },
            methods: {
                loadMore() {
                    if (this.nextUrl === null) {
                        return;
                    }

                    axios.get(this.nextUrl, {params: {count: 6}}).then((response) => {
                        // console.log(response.data);

                        this.userList = [
                            ...this.userList,
                            ...response.data.users,
                        ];

                        this.response = response.data;
                        this.nextUrl = response.data.links.next_url;
                    });
                }
            }
        };


        createApp(rootComponent).mount('#app')
    </script>


  </div>
</main>

<footer class="footer mt-auto py-3 bg-body-tertiary">
  <div class="container">
    <span class="text-body-secondary"> </span>
  </div>
</footer>
<script src="/docs/5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>



</body>

</html>
