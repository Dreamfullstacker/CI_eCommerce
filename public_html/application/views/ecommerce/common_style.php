<style type="text/css">
  .section .section-title{font-size: 16px!important;}
  .section .section-title:before{width:15px!important;float:none!important;}
  .invoice .invoice-title .invoice-number{font-size: inherit !important;}
  .no_border{border-width: 0;}  
  .btn-block{font-weight: bold;font-size: 14px !important;}
  .bordered{border:.5px solid rgba(0, 0, 0, 0.125);}
  .bordered-right{border-right:.5px solid rgba(0, 0, 0, 0.125);}
  .row.bordered{border-top:.5px solid #e4e6fc;background-color: #f9f9f9;}
  .card .card-header{min-height: auto !important;}
  
  #sticky-footer{
    position: fixed;
    bottom: 0;
    left: 0;
    z-index: 100;
    text-align: center;
  }
  #sticky-footer .breadcrumb {
    border-top-left-radius: 1.5rem;
    border-top-right-radius: 1.5rem;
  }
  #sticky-footer .breadcrumb-item a {
    font-size: 14px;
  }
  #sticky-footer .breadcrumb-item a div {
    padding-top: .5rem;
    color: #fff;
  }
  #sticky-footer .breadcrumb-item i {
    font-size: 24px;
    margin: 0;
    color: #fff;
  }
  #sticky-footer a {
    color: #fff;
    text-decoration: none;
  }
  /* .search_form #search{height: 40px;} */
  .search_form #submit{height: 40px;border-radius: 0 20px 20px 0!important;}
  .carousel-caption,.carousel-control-prev-icon{text-shadow: 2px 2px 8px #000 !important;color:#fff !important;}
  .tab-content > .tab-pane .media {line-height: 20px}
  .selectgroup-pills .selectgroup-button{border-radius: 0 !important;border:none;background: none;}
  .selectgroup-pills .selectgroup-item{margin:0;}
  .selectgroup-button-icon{padding: 0 ;}
  .font-14{font-size: 14px !important;}
  .select2-selection--multiple{padding-left:10px !important;padding-right:10px !important;padding-top: 4px !important;}
  .selecttwo{height: 48px !important;}
  .input-group-prepend{width: 45px;}
  /* .input-group-text{font-weight: bold;width: 100%;text-align: center;} */
  .collpase_link{text-decoration: none;}
  .invoice .invoice-detail-item .invoice-detail-value{font-size: 16px;}
  .invoice .invoice-detail-item{margin-bottom: 10px;}
   @media (max-width: 767.98px) {
    .section .section-header h1{text-align: center;}
    .breadcrumb-item{margin:0 auto;text-align: center;}
    .breadcrumb-item+.breadcrumb-item::before{content: none;}
  }
  @media (max-width: 575.98px){
  .section .section-header{margin-bottom:0 !important;}
  .section .section-header .float-right{margin-top:0 !important;}
  }
  .dataTables_scrollBody{min-height: 180px !important;}
  .breadcrumb-item+.breadcrumb-item::before{content: none;}   

  /* Dwi added style */
  .header .search_form {
    margin-bottom: -2rem;
    border-radius: 50rem;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    -moz-box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
  }
  .search_form .input-group .form-control {
    border: 0;
    border-top-left-radius: 50rem;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 50rem;
  }
  .search_form .input-group .input-group-text {
    border: 0;
    border-top-left-radius: 0;
    border-top-right-radius: 50rem;
    border-bottom-right-radius: 50rem;
    border-bottom-left-radius: 0;
  }
  .yith-wcbsl-badge-wrapper {
    left: 0;
    top: 1rem;
  }
  .yith-wcbsl-badge-wrapper .yith-wcbsl-badge-content {
    border-top-left-radius: 0;
    border-top-right-radius: 50rem;
    border-bottom-right-radius: 50rem;
    border-bottom-left-radius: 0;
    background-color: #2d88ff;
    color: #fff;
    opacity: 1;
  }
  .card-img-top {
    height: auto;
  }
  .product-single .card-img-top-wrapper {
    max-height: 250px;
    overflow: hidden;
  }

  .card .card-body p.card-text.product-single-price {
    font-weight: 700;
  }
  .card-text.product-single-price .sell-price,
  #featured-products-carousel .article-category .sell-price {
    color: #dc2626 !important;
  }
  .card-text.product-single-price .original-price {
    display: none;
  }
  .d-grid .btn {
    width: 100%;
  }
  .d-grid.gap-2 .btn {
    margin-bottom: .5rem;
  }

  h4.card-title {
    font-size: 1rem;
  }

  @media (max-width: 767.98px) {
    .product-single .card-img-top-wrapper {
      max-height: 150px;
    } 
  }
  #featured-products-carousel .article-category .original-price,
  .carousel-control-next, .carousel-control-prev {
    display: none;
  }
  .carousel-indicators li {
    width: 10px;
    height: 10px;
    border-radius: 50rem;
  }
  .product-attribute h3 {
    font-size: 1.25rem;
  }
  .product-attribute h4 {
    font-size: .875rem;
    text-transform: capitalize;
  }
  .product-attribute .custom-control {
    display: inline-block;
    padding-left: 0;
    line-height: 1;
  }
  .product-attribute .custom-control-label:after,
  .product-attribute .custom-control-label:before {
    display: none;
  }
  .product-attribute .custom-control-label {
    text-transform: capitalize;
    padding-left: 0;
    font-size: 1rem;
    line-height: 1;
    padding: .5rem .875rem;
    border: 1px solid #ddd;
    border-radius: .25rem;
  }
  .product-attribute .custom-checkbox .custom-control-input:checked~.custom-control-label {
    border-color: #2d88ff;
    color: #2d88ff;
  }
  #sticky-footer-product {
    position: fixed;
    height: 90px;
    border-top-left-radius: 1.5rem;
    border-top-right-radius: 1.5rem;
    z-index: 110;
    bottom: 0;
    left: 0;
    width: 100%;
  }
  #sticky-footer-product .btn {
    border-radius: 50rem;
    width: 10rem;
    font-size: 15px;
  }
  #sticky-footer-product .btn.btn-secondary,
  #sticky-footer-product .btn.btn-secondary:hover,
  #sticky-footer-product .btn.btn-secondary:focus,
  #sticky-footer-product .btn.btn-secondary:active {
    background-color: #fff;
    color: #2d88ff;
  }
</style>