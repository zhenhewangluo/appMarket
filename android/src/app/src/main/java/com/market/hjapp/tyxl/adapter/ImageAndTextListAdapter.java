package com.market.hjapp.tyxl.adapter;

import java.util.List;

import android.app.Activity;
import android.graphics.drawable.Drawable;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;

import com.market.hjapp.R;
import com.market.hjapp.tyxl.AdP;
import com.market.hjapp.tyxl.AsyncImageLoader;
import com.market.hjapp.tyxl.ViewCache;
import com.market.hjapp.tyxl.AsyncImageLoader.ImageCallback;
import com.market.hjapp.tyxl.object.ImageAndText;

public class ImageAndTextListAdapter extends ArrayAdapter<ImageAndText> {

	private ListView listView;
	Activity a;
	private AsyncImageLoader asyncImageLoader;

	public ImageAndTextListAdapter(Activity activity,
			List<ImageAndText> imageAndTexts, ListView listView) {
		// this.adp=activity;
		super(activity, 0, imageAndTexts);
		this.a = activity;
		this.listView = listView;
		asyncImageLoader = new AsyncImageLoader();
	}

	public View getView(int position, View convertView, ViewGroup parent) {

		final int pos = position;
		Activity activity = (Activity) getContext();
		View rowView = convertView;
		ViewCache viewCache;
		if (rowView == null) {
			LayoutInflater inflater = activity.getLayoutInflater();
			rowView = inflater.inflate(R.layout.mainlist1, null);
			viewCache = new ViewCache(rowView);
			rowView.setTag(viewCache);
		} else {
			viewCache = (ViewCache) rowView.getTag();
		}
		ImageAndText imageAndText = getItem(position);
		// Load the image and set it on the ImageView
		String imageUrl = imageAndText.getImageUrl();
		ImageView imageView = viewCache.getImageView();
		imageView.setTag(imageUrl);

		Drawable cachedImage = asyncImageLoader.loadDrawable(imageUrl,
				new ImageCallback() {
					public void imageLoaded(Drawable imageDrawable,
							String imageUrl) {
						ImageView imageViewByTag = (ImageView) listView
								.findViewWithTag(imageUrl);
						if (imageViewByTag != null) {
							imageViewByTag.setImageDrawable(imageDrawable);
						}
					}
				});
		if (cachedImage == null) {
			imageView.setImageResource(R.drawable.default_image);
		} else {
			imageView.setImageDrawable(cachedImage);
		}
		// Set the text on the TextView
		TextView textView1 = viewCache.getTextView();
		textView1.setText(imageAndText.getGiftId());
		TextView textView2 = viewCache.getJianJieView();
		textView2.setText(imageAndText.getGiftName());
		TextView textView3 = viewCache.getJiFenView();
		textView3.setText(imageAndText.getScore());
		Button button = viewCache.getButtonView();

		button.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View arg0) {
				((AdP)a).alertDialog(a, 0, pos);
			}
		});
		return rowView;
	}

	public static int[] Item_checked(int position) {
		return null;

	}
}
