<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
  <title>Zerocryptopoker</title>
  <meta name="description" content="Zerocryptopoker" />
  <meta name="keywords" content="" />
  <link href="{{ asset('v2/home/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('v2/home/css/style.css') }}" rel="stylesheet" type="text/css" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="icon" href="{{ asset('v2/home/images/favicon.ico') }}" type="image/x-icon"/>
</head>
<body>
<div class="header">
  <div class="container">
    <div class="header-wrapper">
      <div class="row header-row">
        <div class="col-lg-6">
          <div class="header-flex">
            <div class="logo"><a href="index.html"><img src="{{ asset('v2/home/images/logo.png') }}" class="img-fluid" /></a></div>
            <div class="header-menu">
              <ul>
                <li><a href="https://www.google.com/">Responsible Gaming</a></li>
                <li><a href="https://www.google.com/">Help</a></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="header-flex end">
            @if(!$user)
              <div class="header-btn button-border"><a href="/login">Login</a></div>
              <div class="header-btn"><a href="/register">Join</a></div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="slider">
  <div class="slider-img">
    <img src="{{ asset('v2/home/images/slider.jpg') }}" class="img-fluid" />
  </div>
</div>
<div class="welcom-section">
  <div class="container">
    <div class="main-sub-head"><h3>Welcome you to Zerocryptopoker</h3></div>
    <div class="main-pare"><p>Make sure to use our available welcome offers when you register.</p></div>
    <div class="commen-btn"><a href="/games/casino-holdem">Play</a></div>
  </div>
</div>
<div class="enjoy-play">
  <div class="container">
    <div class="main-head"><h2>Enjoy our exciting games</h2></div>
    <div class="row">
      <div class="col-md-6">
        <div class="enjoy-play-content">
          <div class="enjoy-play-img"><img src="{{ asset('v2/home/images/banner01.jpg') }}" class="img-fluid" /></div>
          <div class="banner-text-btn">
            <h5>Tournaments</h5>
            <div class="commen-btn"><a href="/games/casino-holdem">Play</a></div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="enjoy-play-content">
          <div class="enjoy-play-img"><img src="{{ asset('v2/home/images/banner02.jpg') }}" class="img-fluid" /></div>
          <div class="banner-text-btn">
            <h5>Cash Game</h5>
            <div class="commen-btn"><a href="/games/casino-holdem">Play</a></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="text-content">
  <div class="container">
    <div class="text-content-pare">
      <div class="commen-text">
        <div class="main-head"><h2>Learn how to play poker</h2></div>
        <div class="main-pare"><p>Whether you are new to the game of poker, or just need a refresher, PokerStars has what you need to learn how to play.</p></div>
      </div>
      <div class="text-ul">
        <ul>
          <li>Learn what beats what: We’ve got a handy guide to the hierarchy of poker hands, so you’ll always know when you’re holding the nuts!</li>
          <li>We’ve got links to help you learn different types of poker game, plus free strategy advice.</li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="card-section">
  <div class="container">
    <div class="commen-text">
      <div class="main-head"><h2>Texas Hold’em - Hand Ranking</h2></div>
      <div class="main-pare"><p>Before you hit the online poker tables, you’ll need to familiarise yourself with the basic hand rankings and rules that govern Texas Hold’em. Here are the 10 hands every player should know before joining the action.</p></div>
    </div>
    <div class="card-row">
      <div class="row">
        <div class="col-md-4">
          <div class="card-col">
            <div class="card-images"><img src="{{ asset('v2/home/images/card01.jpg') }}" class="img-fluid" /></div>
            <div class="main-head"><h2>Royal Flush</h2></div>
            <div class="main-pare"><p>Poker’s most famous hand, a royal flush, cannot be beaten. It consists of the ace, king, queen, jack and ten of a single suit.</p></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card-col">
            <div class="card-images"><img src="{{ asset('v2/home/images/card02.jpg') }}" class="img-fluid" /></div>
            <div class="main-head"><h2>Straight Flush</h2></div>
            <div class="main-pare"><p>Five cards in sequence, of the same suit. In the event of a tie, the highest rank at the top of the sequence wins.</p></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card-col">
            <div class="card-images"><img src="{{ asset('v2/home/images/card03.jpg') }}" class="img-fluid" /></div>
            <div class="main-head"><h2>Four of a Kind</h2></div>
            <div class="main-pare"><p>Four cards of the same rank, and one side card or ‘kicker’. In the event of a tie, the player with the highest side card (‘kicker’) wins.</p></div>
          </div>
        </div>
      </div>
    </div>
    <div class="card-row">
      <div class="row">
        <div class="col-md-4">
          <div class="card-col">
            <div class="card-images"><img src="{{ asset('v2/home/images/card04.jpg') }}" class="img-fluid" /></div>
            <div class="main-head"><h2>Full House</h2></div>
            <div class="main-pare"><p>Three cards of the same rank, and two cards of a different, matching rank. In the event of a tie, the highest three matching cards wins.</p></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card-col">
            <div class="card-images"><img src="{{ asset('v2/home/images/card05.jpg') }}" class="img-fluid" /></div>
            <div class="main-head"><h2>Flush</h2></div>
            <div class="main-pare"><p>Five cards of the same suit, not in sequence. In the event of a tie, the player holding the highest ranked card wins.</p></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card-col">
            <div class="card-images"><img src="{{ asset('v2/home/images/card06.jpg') }}" class="img-fluid" /></div>
            <div class="main-head"><h2>Straight</h2></div>
            <div class="main-pare"><p>Five non-suited cards in sequence. In the event of a tie, the highest ranking card at the top of the sequence wins.</p></div>
          </div>
        </div>
      </div>
    </div>
    <div class="card-row">
      <div class="row">
        <div class="col-md-4">
          <div class="card-col">
            <div class="card-images"><img src="{{ asset('v2/home/images/card07.jpg') }}" class="img-fluid" /></div>
            <div class="main-head"><h2>Three of a Kind</h2></div>
            <div class="main-pare"><p>Three cards of the same rank, and two unrelated side cards. In the event of a tie, the player with the highest, and if necessary, second-highest side card (‘kicker’) wins.</p></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card-col">
            <div class="card-images"><img src="{{ asset('v2/home/images/card08.jpg') }}" class="img-fluid" /></div>
            <div class="main-head"><h2>Two Pair</h2></div>
            <div class="main-pare"><p>Two cards of matching rank, two cards of different matching rank, and one kicker. If both players have an identical Two Pair, the highest kicker wins.</p></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card-col">
            <div class="card-images"><img src="{{ asset('v2/home/images/card09.jpg') }}" class="img-fluid" /></div>
            <div class="main-head"><h2>Pair</h2></div>
            <div class="main-pare"><p>Two cards of matching rank, and three unrelated side cards. In the event of a tie, the player with the highest, and if necessary, second or third-highest side card wins.</p></div>
          </div>
        </div>
      </div>
    </div>
    <div class="card-row">
      <div class="row">
        <div class="col-md-4">
          <div class="card-col">
            <div class="card-images"><img src="{{ asset('v2/home/images/card10.jpg') }}" class="img-fluid" /></div>
            <div class="main-head"><h2>High Card</h2></div>
            <div class="main-pare"><p>Any hand that does not qualify under the categories listed. In the event of a tie, the highest card wins, such as ‘ace-high’.</p></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="media-section">
  <div class="container">
    <div class="commen-text">
      <div class="main-head"><h2>Develop your Skills - Why you should learn with PokerStars</h2></div>
      <div class="main-pare"><p>The world’s largest poker site has everything you need to become poker’s next big-name pro. From free tournaments to expert tips, check out the tools below and start improving your game.</p></div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="media-col">
          <div class="media-images">
            <img src="{{ asset('v2/home/images/media01.jpg') }}" class="img-fluid" />
          </div>
          <div class="media-right">
            <div class="main-head"><h2>Free Poker</h2></div>
            <div class="main-pare"><p>Visit our page that lets you learn the basics and start playing for fun. It’s got everything you need to improve your game - and best of all it’s completely free to join!</p></div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="media-col">
          <div class="media-images">
            <img src="{{ asset('v2/home/images/media02.jpg') }}" class="img-fluid" />
          </div>
          <div class="media-right">
            <div class="main-head"><h2>Try Different Games</h2></div>
            <div class="main-pare"><p>Think you’ve mastered No-Limit Hold’em? Fine tune your skills in a variety of poker formats such as and more in tourneys and Ring Games starting now. Learn every format to become the ultimate poker player.</p></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="footer">
  <div class="container">
    <div class="row">
      <div class="col-lg-3">
        <div class="footer-logo">
          <a href="index.html"><img src="{{ asset('v2/home/images/logo.png') }}" class="img-fluid" /></a>
        </div>
      </div>
      <div class="col-lg-3">
        <div class="footer-ul">
          <div class="main-head"><h2>How To</h2></div>
          <ul>
            <li><a href="https://www.google.com/">Deposits & withdrawals</a></li>
            <li><a href="https://www.google.com/">How to play</a></li>
            <li><a href="https://www.google.com/">Play on mobile</a></li>
            <li><a href="https://www.google.com/">Help</a></li>
          </ul>
        </div>
      </div>
      <div class="col-lg-3">
        <div class="footer-ul">
          <div class="main-head"><h2>Events & Community</h2></div>
          <ul>
            <li><a href="https://www.google.com/">Cash Game</a></li>
            <li><a href="https://www.google.com/">Poker tournaments</a></li>
          </ul>
        </div>
      </div>
      <div class="col-lg-3">
        <div class="footer-ul">
          <div class="main-head"><h2>Legal</h2></div>
          <ul>
            <li><a href="https://www.google.com/">Terms of Service</a></li>
            <li><a href="https://www.google.com/">Privacy Policy</a></li>
            <li><a href="https://www.google.com/">Security of account balances</a></li>
            <li><a href="https://www.google.com/">Responsible Gaming</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="footer-bottom">
  <div class="container">
    <div class="row">
      <div class="col-lg-9">
        <div class="footer-text">
          <div class="main-pare"><p>Copyright © 2001-2021, Rational Intellectual Holdings Limited (‘RIHL’). All rights reserved. ‘ZeroCryptoPoker, the ‘Spade logo’ and all other trademarks, service marks and logos used on this website are property of RIHL. The Copyright, trademarks, service marks and logos of RIHL are used by Sachiko Gaming Private Limited under licence.</p></div>
          <div class="main-pare"><p>This game involves an element of financial risk and may be addictive. Please play responsibly and at your own risk.</p></div>
          <div class="main-pare"><p><a href="https://www.google.com/">Terms of Service</a> | <a href="https://www.google.com/">Privacy Policy</a> | <a href="https://www.google.com/">Responsible Gaming</a></p></div>
        </div>
      </div>
      <div class="col-lg-3">
        <div class="footer-ul-icon">
          <ul>
            <li><a href="https://www.facebook.com/"><i class="fab fa-facebook-f"></i></a></li>
            <li><a href="https://twitter.com/?lang=en"><i class="fab fa-twitter"></i></a></li>
            <li><a href="https://www.youtube.com/"><i class="fab fa-youtube"></i></a></li>
            <li><a href="https://www.instagram.com/"><i class="fab fa-instagram"></i></a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>