 <!--- Sidemenu -->
 <div id="sidebar-menu">
     <!-- Left Menu Start -->
     <ul class="metismenu list-unstyled" id="side-menu">
         <li class="menu-title">Main</li>

         {{-- Products Management --}}
         <li>
             <a href="javascript: void(0);" class="has-arrow waves-effect">
                 <i class="mdi mdi-home"></i>
                 <span>Product Management</span>
             </a>
             <ul class="sub-menu" aria-expanded="false">
                 <li>
                     <a href="{{ route('home') }}" class="waves-effect">
                         <i class="mdi mdi-home"></i><span class="badge bg-primary float-end"></span>
                         <span>List Products</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('create') }}" class="waves-effect">
                         <i class="mdi mdi-home"></i><span class="badge bg-primary float-end"></span>
                         <span>Create Product</span>
                     </a>
                 </li>
             </ul>
         </li>

         {{-- Companies management --}}
         <li>
             <a href="javascript: void(0);" class="has-arrow waves-effect">
                 <i class="mdi mdi-home"></i>
                 <span>Company Management</span>
             </a>
             <ul class="sub-menu" aria-expanded="false">
                 <li>
                     <a href="{{ route('home') }}" class="waves-effect">
                         <i class="mdi mdi-home"></i><span class="badge bg-primary float-end"></span>
                         <span>List Companies</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('home') }}" class="waves-effect">
                         <i class="mdi mdi-home"></i><span class="badge bg-primary float-end"></span>
                         <span>Create Company</span>
                     </a>
                 </li>
             </ul>
         </li>

         {{-- Shapes Management --}}
         <li>
             <a href="javascript: void(0);" class="has-arrow waves-effect">
                 <i class="mdi mdi-home"></i>
                 <span>Shape Management</span>
             </a>
             <ul class="sub-menu" aria-expanded="false">
                 <li>
                     <a href="{{ route('home') }}" class="waves-effect">
                         <i class="mdi mdi-home"></i><span class="badge bg-primary float-end"></span>
                         <span>List Shapes</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('home') }}" class="waves-effect">
                         <i class="mdi mdi-home"></i><span class="badge bg-primary float-end"></span>
                         <span>Create Shape</span>

                     </a>
                 </li>
             </ul>
         </li>

         {{-- Users Management --}}
         <li>
             <a href="javascript: void(0);" class="has-arrow waves-effect">
                 <i class="mdi mdi-home"></i>
                 <span>Users Management</span>
             </a>
             <ul class="sub-menu" aria-expanded="false">
                 <li>
                     <a href="{{ route('home') }}" class="waves-effect">
                         <i class="mdi mdi-home"></i><span class="badge bg-primary float-end"></span>
                         <span>List Users</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('home') }}" class="waves-effect">
                         <i class="mdi mdi-home"></i><span class="badge bg-primary float-end"></span>
                         <span>Create User</span>

                     </a>
                 </li>
             </ul>
         </li>

     </ul>
 </div>
 <!-- Sidebar -->
