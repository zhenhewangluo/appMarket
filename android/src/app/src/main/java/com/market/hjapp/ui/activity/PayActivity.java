
package com.market.hjapp.ui.activity;

import java.util.HashMap;
import java.util.concurrent.RejectedExecutionException;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Toast;

import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.ui.tasks.AnonymousLoginTask;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;
import com.market.hjapp.ui.tasks.PayTask;

public class PayActivity extends BaseActivity
{
	private static final String TAG = "PayActivity";

	public static final int REQUEST_LOGIN = 101;
	public static final int REQUEST_CHARGE_LIST = 102;

	private TaskResultListener mAnonymousLoginTaskResultListener = new TaskResultListener()
	{

		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> res)
		{

			if (!success)
			{
				if (res == null)
					Toast.makeText(getApplicationContext(),
							R.string.error_http_timeout, Toast.LENGTH_LONG)
							.show();

				payFailed();
			} else
			{
				startLogin();

			}
		}

	};

	private void startLogin()
	{
		Intent i = new Intent(getApplicationContext(),
				LoginDialogActivity.class);

		String hint;
		if (GeneralUtil.getUserEmail() != null)
			hint = getString(R.string.login_hint_pay_pwd, mPrice);
		else
			hint = getString(R.string.login_hint_pay_login, mPrice);

		i.putExtra("hint", hint);
		i.putExtra("page_no", 33);

		startActivityForResult(i, REQUEST_LOGIN);
	}

	private TaskResultListener mPayTaskResultListener = new TaskResultListener()
	{

		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> res)
		{
			// TODO Auto-generated method stub

			if (!success)
			{
				if (res == null)
				{
					Toast.makeText(getApplicationContext(),
							R.string.error_http_timeout, Toast.LENGTH_LONG)
							.show();

					payFailed();
				} else
				{
					String error = (String) res.get("errno");
					if (error.equals("E008"))
					{
						// Unauthorized access, need login
						startLogin();
					} else if (error.equals("E133"))
					{ // TEMP wrong code number
						// balance is not enough
						// need charge
						Intent i = new Intent(getApplicationContext(),
								ChargeDialogActivity.class);
						startActivityForResult(i, REQUEST_CHARGE_LIST);

					} else if (error.equals("E124"))
					{
						Toast.makeText(getApplicationContext(),
								R.string.error_pay_wrong_appid,
								Toast.LENGTH_LONG).show();

						payFailed();
					} else
					{
						payFailed();
					}
				}

				return;
			} else
			{
				MyLog.d(TAG, "PAY SUCESSFUL!!!!");

				String balance = (String) res.get("balance");
				GeneralUtil.saveBalance(PayActivity.this, balance);

				Intent i = new Intent();
				i.putExtra("payid", (Integer) res.get("payid") + "");

				Toast.makeText(getApplicationContext(),
						R.string.pay_successful, Toast.LENGTH_LONG).show();
				setResult(RESULT_OK, i);
				finish();

			}

		}

	};

	private String mPrice;
	private String mProductId;
	private String mShopId;

	@Override
	protected void onCreate(Bundle savedInstanceState)
	{
		super.onCreate(savedInstanceState);

		Bundle params = getIntent().getExtras();
		mPrice = params.getString("price");
		mShopId = params.getString("shopId");
		mProductId = params.getString("productId");

		try
		{
			float price = Float.parseFloat(mPrice);
			if (price <= 0)
			{
				payFailed();
				return;
			}
		} catch (Exception e)
		{
			payFailed();
			return;
		}

		if (!GeneralUtil.hasInitialized(this))
		{
			try
			{
				new AnonymousLoginTask(PayActivity.this,
						mAnonymousLoginTaskResultListener).execute();
			} catch (RejectedExecutionException e)
			{
				MyLog.e(TAG, "Got exception when execute asynctask!", e);
			}
		} else
		{
			// go to write pay password page
			startLogin();

		}
	}

	private String mPassword;

	public static boolean noBalance = true;

	private void payFailed()
	{
		setResult(RESULT_CANCELED);
		finish();
	}

	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data)
	{

		MyLog.d(TAG, "onActivityResult( " + "requestCode " + requestCode
				+ "resultCode " + resultCode + " )");

		if (requestCode == REQUEST_LOGIN)
		{
			if (resultCode == RESULT_OK)
			{
				// login successful, go to pay
				mPassword = data.getStringExtra("password");

				String userBalance = GeneralUtil.getUserBalance();
				if (userBalance == null
						|| userBalance.equals("")
						|| Float.parseFloat(userBalance) < Float
						.parseFloat(mPrice))
				{
					// no balance or not enough balance
					// need charge
					Toast.makeText(
							getApplicationContext(),
							getString(R.string.error_pay_no_balance,
									userBalance), Toast.LENGTH_LONG).show();

					Intent i = new Intent(getApplicationContext(),
							ChargeDialogActivity.class);
					startActivityForResult(i, REQUEST_CHARGE_LIST);
				} else
				{
					try
					{
						new PayTask(PayActivity.this, mPayTaskResultListener)
						.execute(mShopId, mProductId, mPrice, mPassword);
					} catch (RejectedExecutionException e)
					{
						MyLog.e(TAG, "Got exception when execute asynctask!", e);
					}
				}

			} else
			{
				payFailed();
			}
		} else if (requestCode == REQUEST_CHARGE_LIST)
		{
			if (resultCode == RESULT_OK)
			{
				// charge successful, go to pay

				// temp
				noBalance = false;

				try
				{
					new PayTask(PayActivity.this, mPayTaskResultListener)
					.execute(mShopId, mProductId, mPrice, mPassword);
				} catch (RejectedExecutionException e)
				{
					MyLog.e(TAG, "Got exception when execute asynctask!", e);
				}
			} else
			{
				payFailed();
			}

		}

		super.onActivityResult(requestCode, resultCode, data);
	}

}
