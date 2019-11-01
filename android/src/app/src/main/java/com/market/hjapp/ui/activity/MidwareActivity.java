package com.market.hjapp.ui.activity;

import android.content.Intent;
import android.os.Bundle;

import com.market.hjapp.*;

public class MidwareActivity extends BaseActivity {
	private static final String TAG = "MidwareActivity";

	private static final String BROADCAST_PAY_COMPLETE = "com.market.hjapp.pay.complete";
	private static final String BROADCAST_AUTHENTICATE_COMPLETE = "com.market.hjapp.authenticate.complete";

	private static final int REQUEST_PAY = 101;
	private static final int REQUEST_AUTHENTICATE = 102;

	private String mDESKey;

	@SuppressWarnings("unchecked")
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);

		String keyEncode = getIntent().getStringExtra("keyEncode");
		String stringEncode = getIntent().getStringExtra("stringEncode");

		String key = SecurityUtil.getMidwareRSADecrypt(keyEncode);
		MyLog.d(TAG, "key is " + key);

		mDESKey = key;

		SecurityUtil.setDESKey(key);
		String decode = SecurityUtil.getDESDecrypt(stringEncode);
		MyLog.d(TAG, "decode is " + decode);

		String[] decodeString = decode.split(",");
		if (decodeString[0].equals("Pay")) {
			String price = decodeString[1];
			String appid = decodeString[2];
			String productid = decodeString[3];

			MyLog.d(TAG, "price is " + price);
			MyLog.d(TAG, "appid is " + appid);
			MyLog.d(TAG, "productid is " + productid);

			Intent i = new Intent(MidwareActivity.this, PayActivity.class);

			i.putExtra("price", price);
			i.putExtra("shopId", appid);
			i.putExtra("productId", productid);

			startActivityForResult(i, REQUEST_PAY);
		} else if (decodeString[0].equals("Authenticate")) {
			String appid = decodeString[1];

			Intent i = new Intent(MidwareActivity.this,
					AuthenticateActivity.class);
			i.putExtra("appid", appid);
			startActivityForResult(i, REQUEST_AUTHENTICATE);
		}

	}

	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {
		if (requestCode == REQUEST_PAY) {
			if (resultCode == RESULT_OK) {
				String payid = data.getStringExtra("payid");
				sendResultBack(BROADCAST_PAY_COMPLETE, true, payid);
			} else {
				sendResultBack(BROADCAST_PAY_COMPLETE, false, null);
			}

			finish();
		} else if (requestCode == REQUEST_AUTHENTICATE) {
			if (resultCode == RESULT_OK) {
				sendResultBack(BROADCAST_AUTHENTICATE_COMPLETE, true, null);
			} else {
				sendResultBack(BROADCAST_AUTHENTICATE_COMPLETE, false, null);
			}

			finish();
		}

		super.onActivityResult(requestCode, resultCode, data);
	}

	private void sendResultBack(String action, boolean result, String payid) {
		String params = action;
		params = params + "," + ((Boolean) result).toString();

		if (action.equals(BROADCAST_PAY_COMPLETE) && result == true
				&& payid != null) {
			params = params + "," + payid;
		}

		MyLog.d(TAG, " key:" + mDESKey);

		// use DES encrypt parameters
		SecurityUtil.setDESKey(mDESKey);
		String stringEncode = SecurityUtil.getDESEncrypt(params);
		MyLog.d(TAG, " result:" + stringEncode);

		Intent i = new Intent(action);
		i.putExtra("result", stringEncode);
		MidwareActivity.this.sendBroadcast(i);
	}

}
