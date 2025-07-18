<?php echo $header; ?>
<?php echo $navbar; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1>Posts Management</h1>
            
            <div class="mb-3">
                <a href="<?php echo $this->url->link('blog/posts/add'); ?>" class="btn btn-primary">
                    Add New Post
                </a>
            </div>
            
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Date Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                
            </table>
        </div>
    </div>
</div>

<?php echo $footer; ?>