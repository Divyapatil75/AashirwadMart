document.addEventListener('DOMContentLoaded',()=>{
  const slides=[...document.querySelectorAll('.slide')],dots=[...document.querySelectorAll('.dot')];let index=0;
  function showSlide(n){if(!slides.length)return;index=n%slides.length;slides.forEach((s,i)=>s.classList.toggle('active',i===index));dots.forEach((d,i)=>d.classList.toggle('active',i===index));}
  if(slides.length>1){setInterval(()=>showSlide(index+1),5000);dots.forEach((d,i)=>d.addEventListener('click',()=>showSlide(i)));}
  const toggle=document.getElementById('chat-toggle'),box=document.getElementById('chat-box'),close=document.getElementById('chat-close'),form=document.getElementById('chat-form'),input=document.getElementById('chat-input'),log=document.getElementById('chat-log');
  if(toggle&&box){toggle.addEventListener('click',()=>box.classList.toggle('hidden'));close?.addEventListener('click',()=>box.classList.add('hidden'));
    const answers=[
      [/delivery|pincode|pin code|area/i,'We currently deliver only to active PIN codes. PIN code 431005 is enabled. The admin can manage more PIN codes.'],
      [/payment|upi|qr|cash|cod/i,'We offer Cash on Delivery and UPI QR payment. UPI orders remain pending until the store admin checks the real transaction and verifies payment.'],
      [/order|track|status/i,'Log in and open My Orders to view order status. UPI orders need admin payment verification before confirmation.'],
      [/cart|wishlist/i,'Log in to add products to your cart or wishlist. Use the cart page to change quantities and continue to checkout.'],
      [/grocer|stationery|electronic|makeup|phone|accessor|category/i,'Browse our five categories: Groceries, Stationery, Electronics, Makeup and Phone Accessories.'],
      [/contact|phone|email|address|owner/i,'Contact Aashirwad Mart at 8329513245 or admin@ashirwadmart.com. Address: Aashirwad Mart, Main Road, Hindustan Awas.'],
      [/hello|hi|hey/i,'Hello! Welcome to Aashirwad Mart. How can I help you today?']
    ];
    form?.addEventListener('submit',e=>{e.preventDefault();const text=input.value.trim();if(!text)return;const user=document.createElement('p');user.className='user-msg';user.textContent=text;log.append(user);const hit=answers.find(([re])=>re.test(text));const bot=document.createElement('p');bot.className='bot-msg';bot.textContent=hit?hit[1]:'I can help with delivery PIN codes, payments, orders, shopping cart, categories and store contact details. For account-specific help, please contact the store.';log.append(bot);input.value='';log.scrollTop=log.scrollHeight;});
  }
});
