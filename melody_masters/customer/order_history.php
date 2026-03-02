<button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#reviewModal<?php echo $product_id; ?>">
    Rate Product
</button>

<div class="modal fade" id="reviewModal<?php echo $product_id; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content glass-modal text-dark">
            <div class="modal-header">
                <h5 class="modal-title">Write a Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="submit_review.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    <div class="mb-3">
                        <label>Rating (1-5)</label>
                        <select name="rating" class="form-select" required>
                            <option value="5">5 - Excellent</option>
                            <option value="4">4 - Good</option>
                            <option value="3">3 - Average</option>
                            <option value="2">2 - Poor</option>
                            <option value="1">1 - Very Bad</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Comment</label>
                        <textarea name="comment" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="submit_review" class="btn btn-warning">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>