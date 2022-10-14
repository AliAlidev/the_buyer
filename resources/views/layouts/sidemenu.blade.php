 <!--- Sidemenu -->
 <div id="sidebar-menu">
     <!-- Left Menu Start -->
     <ul class="metismenu list-unstyled" id="side-menu">
         {{-- <li class="menu-title">Main</li> --}}

         {{-- Home Page --}}
         <li class="d-flex justify-content-center ">
             <a href="{{ route('home') }}" class=" waves-effect">
                 <span style="font-weight: 900">{{ __('layout/side_bar.home_page') }}</span>
             </a>
         </li>

         {{-- Products Management --}}
         <li>
             <a href="javascript: void(0);" class="has-arrow waves-effect">
                 <i class="fab fa-product-hunt"></i>
                 <span>{{ __('layout/side_bar.product_management') }}</span>
             </a>
             <ul class="sub-menu" aria-expanded="false">
                 <li>
                     <a href="{{ route('product-list') }}" class="waves-effect">
                         <i class="fas fa-list"></i><span class="badge bg-primary float-end"></span>
                         <span>{{ __('layout/side_bar.list_products') }}</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('product-create') }}" class="waves-effect">
                         <i class="fas fa-plus-circle"></i><span class="badge bg-primary float-end"></span>
                         <span>{{ __('layout/side_bar.create_product') }}</span>
                     </a>
                 </li>
             </ul>
         </li>

         {{-- Companies management --}}
         <li>
             <a href="javascript: void(0);" class="has-arrow waves-effect">
                 <i class="mdi mdi-office-building"></i></i>
                 <span>{{ __('layout/side_bar.company_management') }}</span>
             </a>
             <ul class="sub-menu" aria-expanded="false">
                 <li>
                     <a href="{{ route('list-companies') }}" class="waves-effect">
                         <i class="fas fa-list"></i><span class="badge bg-primary float-end"></span>
                         <span>{{ __('layout/side_bar.list_companies') }}</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('company-create') }}" class="waves-effect">
                         <i class="fas fa-plus-circle"></i><span class="badge bg-primary float-end"></span>
                         <span>{{ __('layout/side_bar.create_company') }}</span>
                     </a>
                 </li>
             </ul>
         </li>

         {{-- Shapes Management --}}
         <li>
             <a href="javascript: void(0);" class="has-arrow waves-effect">
                 <i class="mdi mdi-shape"></i>
                 <span>{{ __('layout/side_bar.shape_management') }}</span>
             </a>
             <ul class="sub-menu" aria-expanded="false">
                 <li>
                     <a href="{{ route('list-shapes') }}" class="waves-effect">
                         <i class="fas fa-list"></i><span class="badge bg-primary float-end"></span>
                         <span>{{ __('layout/side_bar.list_shapes') }}</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('shape-create') }}" class="waves-effect">
                         <i class="fas fa-plus-circle"></i><span class="badge bg-primary float-end"></span>
                         <span>{{ __('layout/side_bar.create_shape') }}</span>

                     </a>
                 </li>
             </ul>
         </li>

         {{-- Users Management --}}
         <li>
             <a href="javascript: void(0);" class="has-arrow waves-effect">
                 <i class="fas fa-user-friends"></i>
                 <span>{{ __('layout/side_bar.user_management') }}</span>
             </a>
             <ul class="sub-menu" aria-expanded="false">
                 <li>
                     <a href="{{ route('list-users') }}" class="waves-effect">
                         <i class="fas fa-list"></i><span class="badge bg-primary float-end"></span>
                         <span>{{ __('layout/side_bar.list_users') }}</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('user-create') }}" class="waves-effect">
                         <i class="fas fa-plus-circle"></i><span class="badge bg-primary float-end"></span>
                         <span>{{ __('layout/side_bar.create_user') }}</span>

                     </a>
                 </li>
             </ul>
         </li>

     </ul>
 </div>
 <!-- Sidebar -->
