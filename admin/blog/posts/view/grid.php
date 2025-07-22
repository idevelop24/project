<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Posts</h3>
                    <div class="card-tools">
                        <a href="<?php echo $this->url->link('blog/posts/add'); ?>" class="btn btn-primary">Add New</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Sort Order</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($posts) { ?>
                                <?php foreach ($posts as $post) { ?>
                                    <tr>
                                        <td><?php echo $post['title']; ?></td>
                                        <td><?php echo $post['category_name']; ?></td>
                                        <td><?php echo $post['sort_order']; ?></td>
                                        <td>
                                            <a href="<?php echo $this->url->link('blog/posts/edit', 'id=' . $post['id']); ?>" class="btn btn-primary">Edit</a>
                                            <a href="<?php echo $this->url->link('blog/posts/delete', 'id=' . $post['id']); ?>" class="btn btn-danger">Delete</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="4" class="text-center">No posts found.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    <?php echo $pagination; ?>
                </div>
            </div>
        </div>
    </div>
</div>
