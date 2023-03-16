<?php $LINK = "dashboard"; ?>
<?php require_once("./addons/Session.php"); ?>
<?php
$ALL_PARCELS_NO_LIMIT = listAllParcels();
$ALL_PARCELS = listFewParcels();
$ALL_USERS = listUsers();
$UNDELIVERED_PARCELS = listUndeliveredParcels();
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>ADMIN OCP - Dashboard</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="./assets/css/style.311cc0a03ae53c54945b.css" rel="stylesheet">
</head>

<body class="font-inter antialiased bg-gray-100 text-gray-600" :class="{ 'sidebar-expanded': sidebarExpanded }" x-data="{ page: 'dashboard', sidebarOpen: false, sidebarExpanded: localStorage.getItem('sidebar-expanded') == 'true' }" x-init="$watch('sidebarExpanded', value => localStorage.setItem('sidebar-expanded', value))">
  <script>
    localStorage.setItem('sidebarExpanded', 'true');
    if (localStorage.getItem('sidebar-expanded') == 'true') {
      document.querySelector('body').classList.add('sidebar-expanded');
    } else {
      document.querySelector('body').classList.remove('sidebar-expanded');
    }
  </script>
  <div class="flex h-screen overflow-hidden">
    <?php include_once("./inc/Sidebar.php"); ?>
    <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
      <?php include_once("./inc/Header.php"); ?>
      <main>
        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
          <div class="relative bg-indigo-200 p-4 sm:p-6 rounded-sm overflow-hidden mb-8">
            <div class="absolute right-0 top-0 -mt-4 mr-16 pointer-events-none hidden xl:block" aria-hidden="true"><svg width="319" height="198" xmlns:xlink="http://www.w3.org/1999/xlink">
                <defs>
                  <path id="welcome-a" d="M64 0l64 128-64-20-64 20z" />
                  <path id="welcome-e" d="M40 0l40 80-40-12.5L0 80z" />
                  <path id="welcome-g" d="M40 0l40 80-40-12.5L0 80z" />
                  <linearGradient x1="50%" y1="0%" x2="50%" y2="100%" id="welcome-b">
                    <stop stop-color="#A5B4FC" offset="0%" />
                    <stop stop-color="#818CF8" offset="100%" />
                  </linearGradient>
                  <linearGradient x1="50%" y1="24.537%" x2="50%" y2="100%" id="welcome-c">
                    <stop stop-color="#4338CA" offset="0%" />
                    <stop stop-color="#6366F1" stop-opacity="0" offset="100%" />
                  </linearGradient>
                </defs>
                <g fill="none" fill-rule="evenodd">
                  <g transform="rotate(64 36.592 105.604)">
                    <mask id="welcome-d" fill="#fff">
                      <use xlink:href="#welcome-a" />
                    </mask>
                    <use fill="url(#welcome-b)" xlink:href="#welcome-a" />
                    <path fill="url(#welcome-c)" mask="url(#welcome-d)" d="M64-24h80v152H64z" />
                  </g>
                  <g transform="rotate(-51 91.324 -105.372)">
                    <mask id="welcome-f" fill="#fff">
                      <use xlink:href="#welcome-e" />
                    </mask>
                    <use fill="url(#welcome-b)" xlink:href="#welcome-e" />
                    <path fill="url(#welcome-c)" mask="url(#welcome-f)" d="M40.333-15.147h50v95h-50z" />
                  </g>
                  <g transform="rotate(44 61.546 392.623)">
                    <mask id="welcome-h" fill="#fff">
                      <use xlink:href="#welcome-g" />
                    </mask>
                    <use fill="url(#welcome-b)" xlink:href="#welcome-g" />
                    <path fill="url(#welcome-c)" mask="url(#welcome-h)" d="M40.333-15.147h50v95h-50z" />
                  </g>
                </g>
              </svg></div>
            <div class="relative">
              <h1 class="text-2xl md:text-3xl text-gray-800 font-bold mb-1">Good <?= getGreeting() ?>, <?= $ADMIN_USERNAME ?></h1>
              <p>Here is what’s happening:</p>
            </div>
          </div>
          <div class="grid grid-cols-12 gap-6">
            <div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-white shadow-lg rounded-sm border border-gray-200">
              <div class="px-5 py-5">
                <h2 class="text-lg text-indigo-600 font-semibold mb-2">Users</h2>
                <div class="text-xs font-semibold text-gray-400 uppercase mb-1">Total users</div>
                <div class="flex items-start">
                  <div class="text-3xl font-bold mr-2"><?= count($ALL_USERS); ?></div>
                </div>
              </div>
            </div>

            <div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-white shadow-lg rounded-sm border border-gray-200">
              <div class="px-5 py-5">
                <h2 class="text-lg text-indigo-600 font-semibold mb-2">Parcel</h2>
                <div class="text-xs font-semibold text-gray-400 uppercase mb-1">Total parcels</div>
                <div class="flex items-start">
                  <div class="text-3xl font-bold mr-2"><?= count($ALL_PARCELS_NO_LIMIT); ?></div>
                </div>
              </div>
            </div>

            <div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-4 bg-white shadow-lg rounded-sm border border-gray-200">
              <div class="px-5 py-5">
                <h2 class="text-lg text-indigo-600 font-semibold mb-2">Pending Parcel</h2>
                <div class="text-xs font-semibold text-gray-400 uppercase mb-1">Total pending parcels</div>
                <div class="flex items-start">
                  <div class="text-3xl font-bold mr-2"><?= count($UNDELIVERED_PARCELS); ?></div>
                </div>
              </div>
            </div>
            <div class="col-span-full bg-white shadow-lg rounded-sm border border-gray-200">
              <header class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800">Parcels</h2>
              </header>
              <div class="p-3">
                <div class="overflow-x-auto">
                  <table class="table-auto w-full">
                    <thead class="text-xs uppercase text-gray-400 bg-gray-50 rounded-sm">
                      <tr>
                        <th class="p-2 whitespace-nowrap">
                          <div class="font-semibold text-left">Parcel ID</div>
                        </th>
                        <th class="p-2 whitespace-nowrap">
                          <div class="font-semibold text-left">Title</div>
                        </th>
                        <th class="p-2 whitespace-nowrap">
                          <div class="font-semibold text-left">Weight</div>
                        </th>
                        <th class="p-2 whitespace-nowrap">
                          <div class="font-semibold text-center">Dimensions</div>
                        </th>
                        <th class="p-2 whitespace-nowrap">
                          <div class="font-semibold text-center">Total Pieces</div>
                        </th>
                        <th class="p-2 whitespace-nowrap">
                          <div class="font-semibold text-center">Status</div>
                        </th>
                        <th class="p-2 whitespace-nowrap">
                          <div class="font-semibold text-left">Packaging</div>
                        </th>
                        <th class="p-2 whitespace-nowrap">
                          <div class="font-semibold text-left">Service</div>
                        </th>
                        <th class="p-2 whitespace-nowrap">
                          <div class="font-semibold text-left">Terms</div>
                        </th>
                        <th class="p-2 whitespace-nowrap">
                          <div class="font-semibold text-left">Special Handling</div>
                        </th>
                        <th class="p-2 whitespace-nowrap">
                          <div class="font-semibold text-left">Date</div>
                        </th>
                        <th class="p-2 whitespace-nowrap">
                          <div class="font-semibold text-left"></div>
                        </th>
                      </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                      <?php if (count($ALL_PARCELS)) : ?>
                        <?php foreach ($ALL_PARCELS as $parcel) : ?>
                          <tr class="text-xs">
                            <td class="p-2 whitespace-nowrap">
                              <div class="flex items-center">
                                <a href="parcel-details?parcel_id=<?= $parcel['id']; ?>" class="font-medium text-indigo-500 hover:text-indigo-600"><?= $parcel['id']; ?></a>
                              </div>
                            </td>
                            <td class="p-2 whitespace-nowrap">
                              <div class="flex items-center"><?= $parcel['title']; ?></div>
                            </td>
                            <td class="p-2 whitespace-nowrap">
                              <div class="text-center"><?= $parcel['weight'] ?></div>
                            </td>
                            <td class="p-2 whitespace-nowrap">
                              <div class="text-center"><?= $parcel['dimensions'] ?></div>
                            </td>
                            <td class="p-2 whitespace-nowrap">
                              <div class="text-center"><?= $parcel['total_pieces'] ?></div>
                            </td>
                            <td class="p-2 whitespace-nowrap">
                              <?php if (str_contains(strtolower($parcel['status']), "not")) : ?>
                                <div class="text-left font-medium text-red-500"><?= $parcel['status'] ?></div>
                              <?php else : ?>
                                <div class="text-left font-medium text-green-500"><?= $parcel['status'] ?></div>
                              <?php endif; ?>
                            </td>
                            <td class="p-2 whitespace-nowrap">
                              <div class="text-center"><?= $parcel['packaging'] ?></div>
                            </td>
                            <td class="p-2 whitespace-nowrap">
                              <div class="text-center"><?= $parcel['service'] ?></div>
                            </td>
                            <td class="p-2 whitespace-nowrap">
                              <div class="text-center"><?= $parcel['terms'] ?></div>
                            </td>
                            <td class="p-2 whitespace-nowrap">
                              <div class="text-center"><?= $parcel['special_handling_section'] ?></div>
                            </td>
                            <td class="p-2 whitespace-nowrap">
                              <div class="text-center"><?= date("M dS, Y  H:i A", strtotime($parcel['date'])) ?></div>
                            </td>

                            <td class="p-2">
                              <div class="items-center gap-4 flex">
                                <a href="view-timeline?parcel_id=<?= $parcel['id'] ?>" class="btn btn-xs text-xs whitespace-nowrap text-white bg-indigo-500 hover:bg-indigo-600">Manage timeline</a>

                                <div class="flex gap-2 items-center">
                                  <a href="edit-parcel?parcel_id=<?= $parcel['id'] ?>" class="btn btn-xs text-xs text-white bg-light-blue-500 hover:bg-light-blue-600">Edit</a>
                                  <div x-data="{ modalOpen: false }">
                                    <button class="btn btn-xs text-xs text-white bg-red-500 hover:bg-red-600" @click.prevent="modalOpen = true" aria-controls="danger-modal">Delete</button>
                                    <div class="fixed inset-0 bg-gray-900 bg-opacity-30 z-50 transition-opacity" x-show="modalOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-out duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" aria-hidden="true" style="display: none;"></div>
                                    <div id="danger-modal" class="fixed inset-0 z-50 overflow-hidden flex items-center my-4 justify-center transform px-4 sm:px-6" role="dialog" aria-modal="true" x-show="modalOpen" x-transition:enter="transition ease-in-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in-out duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" style="display: none;">
                                      <div class="bg-white rounded shadow-lg overflow-auto max-w-lg w-full max-h-full" @click.outside="modalOpen = false" @keydown.escape.window="modalOpen = false">
                                        <div class="p-5 flex space-x-4">
                                          <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 bg-red-100"><svg class="w-4 h-4 shrink-0 fill-current text-red-500" viewBox="0 0 16 16">
                                              <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm0 12c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm1-3H7V4h2v5z"></path>
                                            </svg></div>
                                          <div>
                                            <div class="mb-2">
                                              <div class="text-lg font-semibold text-gray-800">Delete Parcel?</div>
                                            </div>
                                            <div class="text-sm mb-10">
                                              <div class="space-y-2">
                                                <p>You can't undo this action once it has been performed.</p>
                                              </div>
                                            </div>
                                            <div class="flex flex-wrap justify-end space-x-2">
                                              <button class="btn-sm border-gray-200 hover:border-gray-300 text-gray-600" @click="modalOpen = false">Cancel</button>
                                              <form action="./handler/parcel.handler.php" method="post">
                                                <button name="delete-parcel" value="<?= $parcel['id'] ?>" class="btn-sm bg-red-500 hover:bg-red-600 text-white">Yes, Delete it</button>
                                              </form>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php else : ?>
                        <tr>
                          <td colspan="12" class="p-2 text-center">
                            <span class="text-sm">No parcel found</span>
                          </td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
  <script src="./assets/js/main.75545896273710c7378c.js"></script>
</body>

</html>