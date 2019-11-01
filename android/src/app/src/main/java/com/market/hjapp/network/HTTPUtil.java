package com.market.hjapp.network;

import android.content.Context;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Bundle;
import android.text.TextUtils;
import android.util.Log;

import com.market.hjapp.ConstantValues;
import com.market.hjapp.MyLog;

import org.apache.http.NameValuePair;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.InetSocketAddress;
import java.net.Proxy;
import java.net.URL;
import java.net.URLEncoder;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;
import java.util.Set;

class HTTPUtil {
	private static final String TAG = "HTTPUtil";
	private static final String DEFAULT_CHARSET = "utf-8";

	protected static List<NameValuePair> getPostRequestParams(Bundle params) {
		if (params.size() == 0) {
			return null;
		}

		List<NameValuePair> valuesPairs = new ArrayList<NameValuePair>();

		Set<String> keys = params.keySet();

		for (Iterator<String> it = keys.iterator(); it.hasNext();) {
			String currElem = (String) it.next();
			MyLog.d(TAG, "curr bundle key :" + currElem);
			MyLog.d(TAG, "curr bundle item :" + params.getString(currElem));
			NameValuePair valuePair = new BasicNameValuePair(currElem,
					params.getString(currElem));
			valuesPairs.add(valuePair);
		}
		return valuesPairs;
	}

	// protected static InputStream getInputStream(String streamUrl) throws
	// IOException{
	//
	// if (TextUtils.isEmpty(streamUrl)) {
	// return null;
	// }
	//
	// // URLConnection cn = new URL(streamUrl).openConnection();
	// // cn.connect();
	// HttpURLConnection cn;
	// String proxyHost = android.net.Proxy.getDefaultHost();
	// if (proxyHost != null) {
	// MyLog.d(TAG, "proxyHost:"+proxyHost);
	// java.net.Proxy p = new java.net.Proxy(java.net.Proxy.Type.HTTP,
	// new InetSocketAddress(android.net.Proxy.getDefaultHost(),
	// android.net.Proxy.getDefaultPort()));
	//
	// cn = (HttpURLConnection) new URL(streamUrl).openConnection(p);
	//
	// } else {
	// cn =(HttpURLConnection) new URL(streamUrl).openConnection();
	//
	// }
	// cn.connect();
	//
	// InputStream stream = cn.getInputStream();
	//
	// return stream;
	// }

	// protected static InputStream getInputStream(HttpResponse response) throws
	// IllegalStateException, IOException {
	// if (response == null) {
	// return null;
	// }
	//
	// HttpEntity entity = response.getEntity();
	//
	// if (entity != null) {
	//
	// InputStream instream = entity.getContent();
	//
	// return instream;
	// }
	//
	// return null;
	// }

	// protected static File getFileFromInputStream(String streamUrl, String
	// localPath) throws IOException {
	// InputStream in = getInputStream(streamUrl);
	//
	// if (in != null) {
	// File localFile = new File(localPath);
	//
	// if (!localFile.exists()) {
	// localFile.createNewFile();
	// }
	//
	// OutputStream out = new FileOutputStream(localFile);
	//
	// byte buf[]=new byte[1024];
	// int len;
	//
	// while((len=in.read(buf))>0) {
	// out.write(buf, 0, len);
	// }
	//
	// out.close();
	//
	// in.close();
	//
	// return localFile;
	// }
	//
	// return null;
	// }

	/*
	 * To convert the InputStream to String we use the BufferedReader.readLine()
	 * method. We iterate until the BufferedReader return null which means
	 * there's no more data to read. Each line will appended to a StringBuilder
	 * and returned as String.
	 */
	// protected static String convertStreamToString(InputStream is) throws
	// IOException {
	//
	// if (is == null) {
	// return null;
	// }
	//
	// BufferedReader reader = new BufferedReader(new InputStreamReader(is));
	// StringBuilder sb = new StringBuilder();
	//
	// String line = null;
	//
	// while ((line = reader.readLine()) != null) {
	// sb.append(line + "\n");
	// }
	//
	// is.close();
	//
	// return sb.toString();
	// }

	// protected static String getHttpGetResponse(String url) throws
	// ClientProtocolException, IOException{
	// MyLog.d(TAG, "Get HTTP response from get >>> " + url);
	//
	// // if (TextUtils.isEmpty(url)) {
	// // return null;
	// // }
	// //
	// // HttpClient httpClient = new DefaultHttpClient();
	// //
	// // HttpGet httpGet = new HttpGet(url);
	// //
	// httpGet.getParams().setParameter(CoreConnectionPNames.CONNECTION_TIMEOUT,
	// ConstantValues.REQUEST_TIME_OUT); //10秒的timeout
	// //
	// // HttpResponse response = httpClient.execute(httpGet);
	// //
	// // MyLog.i("REST:Response Status line",
	// response.getStatusLine().toString());
	// //
	// // return response;
	//
	// String result=null;
	// if (TextUtils.isEmpty(url)) {
	// return null;
	// }
	// HttpURLConnection urlConn;
	// String proxyHost = android.net.Proxy.getDefaultHost();
	// if (proxyHost != null) {
	// MyLog.d(TAG, "proxyHost:"+proxyHost);
	// java.net.Proxy p = new java.net.Proxy(java.net.Proxy.Type.HTTP,
	// new InetSocketAddress(android.net.Proxy.getDefaultHost(),
	// android.net.Proxy.getDefaultPort()));
	//
	// urlConn = (HttpURLConnection) new URL(url).openConnection(p);
	//
	// } else {
	// urlConn =(HttpURLConnection) new URL(url).openConnection();
	//
	// }
	// urlConn.setRequestMethod("GET");
	// urlConn.setDoInput(true);
	// urlConn.setDoOutput(true);
	// urlConn.setUseCaches(false);
	// // urlConn.setRequestProperty("Connection", "Keep-Alive");
	// // urlConn.setRequestProperty("Charset", DEFAULT_CHARSET);
	// urlConn.setConnectTimeout(ConstantValues.REQUEST_TIME_OUT);
	// urlConn.setReadTimeout(ConstantValues.REQUEST_TIME_OUT);
	// urlConn.connect();
	//
	// int responseCode = urlConn.getResponseCode();
	// MyLog.d(TAG, "responseCode="+responseCode);
	// if(HttpURLConnection.HTTP_OK == responseCode){
	//
	// StringBuffer sb = new StringBuffer();
	// String readLine=null;
	// BufferedReader responseReader;
	// responseReader = new BufferedReader(new
	// InputStreamReader(urlConn.getInputStream(), DEFAULT_CHARSET));
	// while ((readLine = responseReader.readLine()) != null) {
	// sb.append(readLine).append("\n");
	// }
	// result=sb.toString();
	// MyLog.d(TAG, "result="+result);
	// if (responseReader!=null) {
	// responseReader.close();
	// }
	// if (urlConn!=null) {
	// urlConn.disconnect();
	// }
	// return result;
	// }
	// else {
	// return null;
	// }
	//
	// }
	protected static String getHttpPostResponse(Context ctx, String url,
			List<NameValuePair> paramsPair) throws ClientProtocolException,
			IOException {
		MyLog.d(TAG, "Get HTTP response from post >>> " + url);
		String result;
		if (TextUtils.isEmpty(url)) {
			return null;
		}

		if (paramsPair == null) {
			return null;
		}
		// String requestString ="";
		// MyLog.d(TAG, "paramsPair size="+paramsPair);
		// for (int i = 0; i < paramsPair.size(); i++) {
		// requestString+="&"+paramsPair.get(i).getName()+"="+paramsPair.get(i).getValue();
		// }
		// MyLog.d(TAG, "paramsPair>>>>>>>>>>>>"+requestString);
		// HttpClient httpClient = new DefaultHttpClient();
		//
		// HttpPost httpPost = new HttpPost(url);
		// httpPost.getParams().setParameter(CoreConnectionPNames.CONNECTION_TIMEOUT,
		// ConstantValues.REQUEST_TIME_OUT);
		//
		// // Add your data
		// HttpEntity entity = new UrlEncodedFormEntity(params, HTTP.UTF_8);
		// httpPost.setEntity(entity);
		//
		// HttpResponse response = httpClient.execute(httpPost);
		// MyLog.i("REST:Response Status line",
		// response.getStatusLine().toString());
		// MyLog.d(TAG, "response content length: " +
		// response.getEntity().getContentLength());
		// MyLog.d(TAG, "response content encoding: " +
		// response.getEntity().getContentEncoding());
		// MyLog.d(TAG, "response content type: " +
		// response.getEntity().getContentType());
		// return response;

		HttpURLConnection urlConn;

		NetworkInfo info = ((ConnectivityManager) ctx
				.getSystemService(Context.CONNECTIVITY_SERVICE))
				.getActiveNetworkInfo();

		if (info == null)
			return null;

		if (info.getType() != ConnectivityManager.TYPE_WIFI) {
			String proxyHost = android.net.Proxy.getDefaultHost();

			// 20120331,zxg,fix cmwap can't connect.
			if (HTTParser.isCMWAP(ctx)) {
				MyLog.e(TAG, "isCMWAP");

				int contentBeginIdx = url.indexOf('/', 7);
				StringBuffer urlStringBuffer = new StringBuffer(
						"http://10.0.0.172:80");

				urlStringBuffer.append(url.substring(contentBeginIdx));
				urlConn = (HttpURLConnection) new URL(
						urlStringBuffer.toString()).openConnection();
				urlConn.setRequestProperty("X-Online-Host",
						url.substring(7, contentBeginIdx));

			} else if (HTTParser.isCTWAP(ctx)) {
				Proxy proxy = new Proxy(Proxy.Type.HTTP,
						new InetSocketAddress("10.0.0.200", 80));
				urlConn = (HttpURLConnection) new URL(url)
						.openConnection(proxy);
			} else if (proxyHost != null) {
				MyLog.d(TAG, "proxyHost:" + proxyHost);
				Proxy p = new Proxy(Proxy.Type.HTTP,
						new InetSocketAddress(
								android.net.Proxy.getDefaultHost(),
								android.net.Proxy.getDefaultPort()));

				urlConn = (HttpURLConnection) new URL(url).openConnection(p);

			} else {
				urlConn = (HttpURLConnection) new URL(url).openConnection();

			}
		} else
			urlConn = (HttpURLConnection) new URL(url).openConnection();

		urlConn.setDoInput(true);
		urlConn.setDoOutput(true);
		urlConn.setRequestMethod("POST");
		urlConn.setUseCaches(false);
		urlConn.setInstanceFollowRedirects(true);
		urlConn.setConnectTimeout(ConstantValues.REQUEST_TIME_OUT);

		// urlConn.setRequestProperty("Content-Type",
		// "application/octet-stream");
		// urlConn.addRequestProperty("Content-Type",
		// "application/x-www-form-urlencod");
		// urlConn.setRequestProperty("Connection", "Keep-Alive");
		// urlConn.setRequestProperty("Charset", DEFAULT_CHARSET);
		// urlConn.setConnectTimeout(ConstantValues.REQUEST_TIME_OUT);
		// urlConn.setReadTimeout(ConstantValues.REQUEST_TIME_OUT);

		MyLog.d(TAG, " urlConn.connect Start>>>");

		urlConn.connect();

		MyLog.d(TAG, " urlConn.connect Done>>>");

		OutputStream outputStream = urlConn.getOutputStream();
		String paramsPairByPost = null;

		MyLog.d(TAG, " paramsPair >>>");

		for (int i = 0; i < paramsPair.size(); i++) {
			// String
			// paramsByPost=paramsPair.get(i).getName()+"="+paramsPair.get(i).getValue();
			// MyLog.d(TAG, "paramsPair>>>"+paramsByPost);
			if (paramsPairByPost == null)
				paramsPairByPost = "";
			else
				paramsPairByPost += "&";

			if (paramsPair.get(i).getValue() != null) {
				// MyLog.d(TAG, "source param>>>"+paramsPair.get(i).getValue());
				// MyLog.d(TAG,
				// "encode param>>>"+URLEncoder.encode(paramsPair.get(i).getValue()));
				// paramsPairByPost+=
				// paramsPair.get(i).getName()+"="+paramsPair.get(i).getValue();
				paramsPairByPost += paramsPair.get(i).getName()
						+ "="
						+ URLEncoder.encode(paramsPair.get(i).getValue(),
								"utf-8");

			} else {
				paramsPairByPost += paramsPair.get(i).getName() + "=";
			}
		}

		Log.d(TAG, "paramsPair>>>---" + paramsPairByPost);
		outputStream.write(paramsPairByPost.getBytes());
		outputStream.flush();
		outputStream.close();

		int responseCode = urlConn.getResponseCode();
		MyLog.d(TAG, "responseCode=" + responseCode);
		if (HttpURLConnection.HTTP_OK == responseCode) {

			StringBuffer sb = new StringBuffer();
			String readLine = null;
			BufferedReader responseReader;
			responseReader = new BufferedReader(new InputStreamReader(
					urlConn.getInputStream(), DEFAULT_CHARSET));

			MyLog.d(TAG, "start to read");
			while ((readLine = responseReader.readLine()) != null) {
				sb.append(readLine).append("\n");
			}
			MyLog.d(TAG, "buffer to string ");
			result = sb.toString();
			MyLog.d(TAG, "result=" + result);

			if (responseReader != null) {
				responseReader.close();
			}

			if (urlConn != null) {
				urlConn.disconnect();
			}
			return result;
		} else {

			if (urlConn != null) {
				urlConn.disconnect();
			}

			return null;
		}

	}

	protected static JSONObject getJsonObject(String result)
			throws IOException, JSONException {
		if (result == null) {
			return null;
		}

		return getJsonObject(result, false);
	}

	// protected static JSONObject getJsonObject(HttpResponse response, boolean
	// isGBK) throws IOException, JSONException{
	//
	// if (response == null) {
	// return null;
	// }
	//
	// HttpEntity entity = response.getEntity();
	//
	// if (entity != null) {
	//
	// String result = EntityUtils.toString(entity, DEFAULT_CHARSET);
	// MyLog.i("REST: result", result);
	// if (isGBK) {
	// MyLog.i("REST: result", "is GBK, decoding...");
	// MyLog.i("REST:","Before decoding :" + result);
	// result = new String(result.getBytes("UTF-8"), "GBK");
	// MyLog.i("REST: result", result);
	// }
	//
	// String utf8str = new String(result.getBytes("UTF-8"));
	//
	// JSONObject json = new JSONObject(utf8str);
	//
	// MyLog.i("REST", "<jsonobject>\n" + json.toString()
	// + "\n</jsonobject>");
	//
	// return json;
	// }
	//
	// return null;
	// }
	//
	// protected static String getResult(HttpResponse response) throws
	// IOException, JSONException{
	//
	// if (response == null) {
	// return null;
	// }
	//
	// HttpEntity entity = response.getEntity();
	//
	// if (entity != null) {
	//
	// String result = EntityUtils.toString(entity, DEFAULT_CHARSET);
	//
	// return result;
	// }
	//
	// return null;
	// }

	protected static JSONObject getJsonObject(String result, boolean isGBK)
			throws IOException, JSONException {
		if (result == null)
			return null;

		if (isGBK) {
			MyLog.i("REST: result", "is GBK, decoding...");
			MyLog.i("REST:", "Before decoding :" + result);
			result = new String(result.getBytes("UTF-8"), "GBK");
			MyLog.i("REST: result", result);
		}

		String utf8str = new String(result.getBytes("UTF-8"));

		JSONObject json = new JSONObject(utf8str);

		MyLog.i("REST", "<jsonobject>\n" + json.toString() + "\n</jsonobject>");

		return json;
	}

	// protected static JSONArray getJsonArray(HttpResponse response) throws
	// IllegalStateException, IOException, JSONException {
	// if (response == null) {
	// return null;
	// }
	//
	// HttpEntity entity = response.getEntity();
	//
	// if (entity != null) {
	//
	// InputStream instream = entity.getContent();
	// String result = convertStreamToString(instream);
	// MyLog.i("REST: result", result);
	//
	// JSONArray json = new JSONArray(result);
	// MyLog.i("REST", "<jsonobject>\n" + json.toString()
	// + "\n</jsonobject>");
	//
	// instream.close();
	//
	// return json;
	// }
	//
	// return null;
	// }

	// protected static NetworkInfo[] getNetworkInfo(Context context){
	// ConnectivityManager manager = (ConnectivityManager)
	// context.getSystemService(Context.CONNECTIVITY_SERVICE);
	// return manager.getAllNetworkInfo();
	// }
}
