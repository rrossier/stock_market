<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Place Order</h3>
  </div>
  <div class="modal-body">
    <p>
      <div class="controls controls-row">
        <label class="control-label span2" for="stockId">Stock Id: </label><input class="input-medium" type="text" name="stockId" id="stockId" value="" disabled/>
      </div>
      <div class="controls controls-row">
        <label class="control-label span2" for="stockname">Stock Name: </label><input class="input-medium" type="text" name="stockname" id="stockname" value="" disabled/>
      </div>
      <div class="controls controls-row">
        <label class="control-label span2" for="qty">Quantity: </label><input class="input-medium" type="text" class="navbar-search input-mini span1" placeholder="Quantity" value="0" name="qty" id="qty" onchange="calculamount()"/>
      </div>
      <div class="controls controls-row">
        <label class="control-label span2" for="position">Position: </label><select class="input-medium" name="position" id="position" onchange="calculamount()"><option selected value="buy">Buy</option><option value="sell">Sell</option></select>
      </div>
      <div class="controls controls-row">
        <label class="control-label span2" for="amount">Amount : </label><input class="input-medium" type="text" name="amount" id="amount" value="" disabled/>
      </div>
      <div class="controls controls-row">
        <label class="control-label span2" for="fees">Broker fees: </label><input class="input-medium" type="text" name="fees" id="fees" value="" disabled/>
      </div>
      <div class="controls controls-row">
        <label class="control-label span2" for="total">Total amount: </label><input class="input-medium" type="text" name="total" id="total" value="" disabled/>
      </div>
      <div class="controls controls-row">
        <label class="control-label span2" for="type_order">Order type: </label>
        <select class="input-medium" name="type_order" id="type_order">
          <option selected value="market">Market</option>
          <option value="limit">Limit</option>
          <option value="stop">Stop</option>
        </select>
      </div>
      <div class="controls controls-row">
        <label class="control-label span2" for="priceorder">Price Order: </label><input class="input-medium" type="text" name="priceorder" id="priceorder" value=""/>
      </div>
      <div class="controls controls-row">
        <label class="control-label span2" for="account">Account: </label><select class="input-medium" name="account" id="account"><option value="perso">Personal</option><?php
        foreach($user->get_list_firms() as $line){
          echo "<option value='".$line['id_firm']."'>".$line['name']."</option>";
        }
        ?></select>
      </div>
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
    <button class="btn btn-primary order">Execute order</button>
  </div>
</div>