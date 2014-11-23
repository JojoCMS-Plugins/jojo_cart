<form action="{$SECUREURL}/cart/process/{$token}/" method="post">
  <input type="hidden" name="handler" value="free" />
  <div class="box">
    <h2>Free transaction</h2>
    <p>There is no charge for this order.</p>
  <div style="text-align: center">
    <button class="btn btn-primary" name="submit" value="Submit order">Submit order</button>
  </div>
</form>