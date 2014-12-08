    <div class="container main content">
        <div class="sixteen columns clearfix breadcrumb">
            <span itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><a href="/" title="Главная" itemprop="url"><span itemprop="title">Главная</span></a></span> &nbsp; / &nbsp;
            Восстановление пароля
        </div>
        <div class="sixteen columns clearfix collection_nav">
            <h1 class="collection_title">Восстановление пароля</h1>
        </div>
        <div class="clearfix"></div>
        <?php if(isset($successMessage) && !empty($successMessage)):?>
        <div class="sixteen columns page">
            <div class="alert alert-success">
                <strong><?=$successMessage;?></strong>
            </div>
        </div>
        <?php else:?>
            <?php if(isset($errorMessage) && !empty($errorMessage)):?>
            <div class="sixteen columns page">
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <strong><?=$errorMessage;?></strong>
                </div>
            </div>
            <?php endif; ?>

			<div class="sixteen columns page">
                <form role="form" method="post" action="/user/password" id="passwordForm">
                    <div class="form-group">
                        <input type="email" required="required" class="form-control" name="email" id="email" placeholder="Введите email" />
						            <span class="input-group-btn">
							            
						            </span>
                    </div>
                    <div class="form-group">
                      <button id="loginbtn" type="submit" class="btn btn-lg">Восстановить</button>
                    </div>
                </form>
			</div>

        <?php endif; ?>
    </div>
  <script>
      jQuery(function($) {
          $('#passwordForm').hzBootstrapValidator();
      });
  </script>