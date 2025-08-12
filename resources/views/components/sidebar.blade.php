  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

      <ul class="sidebar-nav" id="sidebar-nav">

          <li class="nav-item">
              <a class="nav-link " href="{{ route('dashboard') }}">
                  <i class="bi bi-grid"></i>
                  <span>Dashboard</span>
              </a>
          </li><!-- End Dashboard Nav -->

          <li class="nav-item">
              <a class="nav-link collapsed" {{-- @if (auth()->user()->usertype === 'user') data-bs-target="#components-nav" data-bs-toggle="collapse" @endif --}} data-bs-target="#components-nav"
                  data-bs-toggle="collapse" href="#">
                  <i class="bi bi-columns-gap"></i><span>PURCHASE</span><i class="bi bi-chevron-down ms-auto"></i>
              </a>
              <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                  <li>
                      <a href="{{ route('suppliers') }}">
                          <i class="bi bi-building-fill-check"></i><span>Suppliers</span>
                      </a>
                  </li>

                  <li>
                      <a href="{{ route('products') }}">
                          <i class="bi bi-list-task"></i><span>Products</span>
                      </a>
                  </li>

                  <li>
                      <a href="{{ route('purchase_orders.index') }}">
                          <i class="bi bi-cart4"></i><span>Purchase Orders</span>
                      </a>
                  </li>
                  <li>
                      <a href="{{ route('grns.index') }}">
                          <i class="bi bi-cart-plus-fill"></i><span>Receiving - GRN</span>
                      </a>
                  </li>
                  <li>
                      <a href="{{ route('purchase_returns.index') }}">
                          <i class="bi bi-cart-x"></i><span>Purchase Return</span>
                      </a>
                  </li>
              </ul>
          </li><!-- End Components Nav -->

          <li class="nav-item">
              <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
                  <i class="bi bi-cart"></i><span>Stocks</span><i class="bi bi-chevron-down ms-auto"></i>
              </a>
              <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                  <li>
                      <a href="{{ route('stock.index') }}">
                          <i class="bi bi-cart-fill"></i><span>Current Stock</span>
                      </a>
                  </li>
                  <li>
                      <a href="{{ route('lowStock') }}">
                          <i class="bi bi-cart3"></i><span>Low Stock Products</span>
                      </a>
                  </li>
                  <li>
                      <a href="{{ route('stockAdj') }}">
                          <i class="bi bi-sliders2-vertical"></i><span>Stock Adjustment</span>
                      </a>
                  </li>
              </ul>
          </li><!-- End Forms Nav -->

          <li class="nav-item">
              <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
                  <i class="bi bi-layout-text-window-reverse"></i><span>SALES</span><i
                      class="bi bi-chevron-down ms-auto"></i>
              </a>
              <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">

                  <li>
                      <a href="{{ route('customers.index') }}">
                          <i class="bi bi-person-check-fill"></i><span>Customers</span>
                      </a>
                  </li>
                  <li>
                      <a href="{{ route('vehicles.index') }}">
                          <i class="bi bi-bicycle"></i><span>Vehicles</span>
                      </a>
                  </li>
                  <li>
                      <a href="{{ route('jobtypes.index') }}">
                          <i class="bi bi-tools"></i><span>Job Types</span>
                      </a>
                  </li>
                  <li>
                      <a href="{{ route('quotations.index') }}">
                          <i class="bi bi-card-checklist"></i><span>Quotation</span>
                      </a>
                  </li>
                  <li>
                      <a href="{{ route('saleInvoice') }}">
                          <i class="bi bi-bag-fill"></i><span>Sales Invoice</span>
                      </a>
                  </li>

                  <li>
                      <a href="{{ route('INVReturn') }}">
                          <i class="bi bi-receipt"></i><span>Invoice Return</span>
                      </a>
                  </li>
                  <li>
                      <a href="{{ route('workOrder') }}">
                          <i class="bi bi-receipt-cutoff"></i><span>Service Invoice</span>
                      </a>
                  </li>

                  <li>
                      <a href="{{ route('serviceReminder') }}">
                          <i class="bi bi-bell-fill"></i><span>Servie Reminder</span>
                      </a>
                  </li>
              </ul>
          </li><!-- End Tables Nav -->

          <li class="nav-item">
              <a class="nav-link collapsed" data-bs-target="#payments-nav" data-bs-toggle="collapse" href="#">
                  <i class="bi bi-credit-card-2-front"></i><span>PAYMENTS</span><i class="bi bi-chevron-down ms-auto"></i>
              </a>
              <ul id="payments-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                  <li>
                      <a href="{{ route('payment-transactions.dashboard') }}">
                          <i class="bi bi-speedometer2"></i><span>Payment Dashboard</span>
                      </a>
                  </li>
                  <li>
                      <a href="{{ route('payment-transactions.index') }}">
                          <i class="bi bi-journal-text"></i><span>Payment Transactions</span>
                      </a>
                  </li>
                  <li>
                      <a href="{{ route('payment-transactions.create', ['type' => 'cash_in']) }}">
                          <i class="bi bi-cash-coin"></i><span>Cash In</span>
                      </a>
                  </li>
                  <li>
                      <a href="{{ route('payment-transactions.create', ['type' => 'cash_out']) }}">
                          <i class="bi bi-cash-stack"></i><span>Cash Out</span>
                      </a>
                  </li>
                  <li>
                      <a href="{{ route('payment-methods.index') }}">
                          <i class="bi bi-credit-card"></i><span>Payment Methods</span>
                      </a>
                  </li>
                  <li>
                      <a href="{{ route('bank-accounts.index') }}">
                          <i class="bi bi-bank"></i><span>Bank Accounts</span>
                      </a>
                  </li>
                  <li>
                      <a href="{{ route('payment-categories.index') }}">
                          <i class="bi bi-tags"></i><span>Payment Categories</span>
                      </a>
                  </li>
              </ul>
          </li><!-- End Payments Nav -->

          <li class="nav-item">
              <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
                  <i class="bi bi-pie-chart-fill"></i><span>STATISITICS</span><i class="bi bi-chevron-down ms-auto"></i>
              </a>
              <ul id="charts-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                  <li>
                      <a href="{{ route('overview') }}">
                          <i class="bi bi-graph-up-arrow"></i><span>Overview</span>
                      </a>
                  </li>
                  <li>
                      <a href="{{ route('insights') }}">
                          <i class="bi bi-bar-chart-fill"></i><span>Insights</span>
                      </a>
                  </li>
                  <li>
                      <a href="{{ route('reports') }}">
                          <i class="bi bi-clipboard-data-fill"></i><span>Reports</span>
                      </a>
                  </li>
              </ul>
          </li><!-- End Charts Nav -->

          <li class="nav-item">
              <a class="nav-link collapsed">
                  <i class="bi bi-building-gear"></i><span>BACK OFFICE</span><i class="bi bi-chevron-down ms-auto"></i>
              </a>
              <ul id="icons-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                  <li>
                      <a href="{{ route('genSettting') }}">
                          <i class="bi bi-gear-wide-connected"></i><span>General Settings</span>
                      </a>
                  </li>
                  <li>
                      <a href="{{ route('staffManagement') }}">
                          <i class="bi bi-person-gear"></i><span>Staff Managemkent</span>
                      </a>
                  </li>
                  <li>
                      <a href="{{ route('events') }}">
                          <i class="bi bi-list-columns-reverse"></i><span>Events</span>
                      </a>
                  </li>
              </ul>
          </li><!-- End Icons Nav -->

          <hr>

          <li class="nav-item">
              <a class="nav-link collapsed" href="{{ route('profile.show') }}">
                  <i class="bi bi-person-circle"></i>
                  <span>Profile</span>
              </a>
          </li><!-- End Profile Page Nav -->

          <li class="nav-item">
              <a class="nav-link collapsed" href="{{ route('service-schedules.index') }}">
                  <i class="bi bi-calendar-check"></i>
                  <span>Service Schedules</span>
              </a>
          </li>

          <li class="nav-item">
              <a class="nav-link collapsed" href="{{ route('appointments.index') }}">
                  <i class="bi bi-calendar-week"></i>
                  <span>Appointments</span>
              </a>
          </li><!-- End F.A.Q Page Nav -->

          <li class="nav-item">
              <a class="nav-link collapsed" href="{{ route('notifications.index') }}">
                  <i class="bi bi-bell"></i>
                  <span>Notifications</span>
              </a>
          </li>

          <li class="nav-item">
              <a class="nav-link collapsed" href="{{ route('logout') }}">
                  <i class="bi bi-box-arrow-left"></i>
                  <span>Log Out</span>
              </a>
          </li><!-- End Contact Page Nav -->



      </ul>

  </aside><!-- End Sidebar-->
